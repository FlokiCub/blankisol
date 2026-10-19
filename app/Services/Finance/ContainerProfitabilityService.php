<?php

namespace App\Services\Finance;

use App\Models\ContainerProfitabilityAnalysis;
use App\Models\EconomicAlert;
use App\Models\EconomicAuditLog;
use App\Models\EconomicParameter;
use App\Models\FixedCostItem;
use App\Models\FixedCostMonthly;
use App\Models\FuelAndTariffRate;
use App\Models\RegionalDispatchRoute;
use App\Models\User;
use Illuminate\Support\Carbon;

class ContainerProfitabilityService
{
    /**
     * Compute full profitability model for a container shipment.
     */
    public function calculate(array $data): array
    {
        $containerId = $data['container_id'] ?? 'CONT-GENERIC';
        $arrivalDate = $data['arrival_date'] ?? now()->toDateString();
        $transitaria = strtoupper($data['transitaria'] ?? 'ENCI');
        $status = $data['status'] ?? 'Cerrado';

        $kgOcc = (float) ($data['kg_occidente'] ?? 0);
        $kgCen = (float) ($data['kg_centro'] ?? 0);
        $kgOri = (float) ($data['kg_oriente'] ?? 0);
        $kgTotal = $kgOcc + $kgCen + $kgOri;

        $otherRevenue = (float) ($data['other_revenue'] ?? 0);
        $otherDirectCosts = (float) ($data['other_direct_costs'] ?? 0);

        // Active rates & parameters
        $rateCard = FuelAndTariffRate::getRateForDate($arrivalDate);
        if (!$rateCard) {
            $rateCard = new FuelAndTariffRate([
                'tariff_occidente_usd_kg' => 0.8000,
                'tariff_centro_usd_kg' => 0.8000,
                'tariff_oriente_usd_kg' => 0.8000,
                'diesel_price_usd_l' => 2.0000,
                'port_appointment_usd' => 10.00,
                'driver_diet_usd' => 10.00,
                'labor_shift_transitaria_usd' => 15.00,
                'labor_shift_transitory_center_usd' => 15.00,
                'transitaria_service_enci_usd' => 0.00,
                'transitaria_service_av_usd' => 0.00,
                'commercial_commission_pct' => 0.0300,
                'commercial_commission_usd_kg' => 0.0300,
                'vehicle_wear_usd_km' => 0.1200,
            ]);
        }

        // Allow overrides (e.g. for sensitivity simulation)
        $dieselPrice = isset($data['custom_diesel_price']) ? (float) $data['custom_diesel_price'] : (float) $rateCard->diesel_price_usd_l;
        $tariffOcc = isset($data['custom_tariff_rate']) ? (float) $data['custom_tariff_rate'] : (float) $rateCard->tariff_occidente_usd_kg;
        $tariffCen = isset($data['custom_tariff_rate']) ? (float) $data['custom_tariff_rate'] : (float) $rateCard->tariff_centro_usd_kg;
        $tariffOri = isset($data['custom_tariff_rate']) ? (float) $data['custom_tariff_rate'] : (float) $rateCard->tariff_oriente_usd_kg;

        // Economic parameters
        $stdViajesPuerto = (float) EconomicParameter::getValue('Std_Viajes_Puerto', 1);
        $litrosPuertoViaje = (float) EconomicParameter::getValue('Litros_Puerto_Viaje', 60);
        $kmPuertoViaje = (float) EconomicParameter::getValue('Km_Puerto_Viaje', 180);
        $rendDesagrupe = (float) EconomicParameter::getValue('Rend_Desagrupe', 1000);
        $rendDesagrupeCT = (float) EconomicParameter::getValue('Rend_Desagrupe_CT', 800);
        $capCamionOCC = (float) EconomicParameter::getValue('Cap_Camion_OCC', 5000);
        $capCamionCEN = (float) EconomicParameter::getValue('Cap_Camion_CEN', 5000);
        $capCamionORI = (float) EconomicParameter::getValue('Cap_Camion_ORI', 5000);
        $lDistOCC = (float) EconomicParameter::getValue('L_Dist_OCC', 120);
        $lTrasCEN = (float) EconomicParameter::getValue('L_Tras_CEN', 250);
        $lDistCEN = (float) EconomicParameter::getValue('L_Dist_CEN', 150);
        $lTrasORI = (float) EconomicParameter::getValue('L_Tras_ORI', 450);
        $lDistORI = (float) EconomicParameter::getValue('L_Dist_ORI', 180);
        $pctReclamaciones = (float) EconomicParameter::getValue('Pct_Reclamaciones', 0.005);
        $pctBanco = (float) EconomicParameter::getValue('Pct_Banco', 0.010);
        $impVentasServ = (float) EconomicParameter::getValue('Imp_Ventas_Serv', 0.100);
        $contribTerritorial = (float) EconomicParameter::getValue('Contrib_Territorial', 0.010);
        $impUtilidades = (float) EconomicParameter::getValue('Imp_Utilidades', 0.350);
        $toleranciaComb = (float) EconomicParameter::getValue('Tolerancia_Comb', 0.100);
        $baseComision = (string) EconomicParameter::getValue('Base_Comision', '% DEL INGRESO');
        $metodoProrrateo = (string) EconomicParameter::getValue('Metodo_Prorrateo', 'KG');
        $kmDistOcc = (float) EconomicParameter::getValue('Km_Dist_OCC', 150);
        $kmTrasCen = (float) EconomicParameter::getValue('Km_Tras_CEN', 300);
        $kmDistCen = (float) EconomicParameter::getValue('Km_Dist_CEN', 200);
        $kmTrasOri = (float) EconomicParameter::getValue('Km_Tras_ORI', 800);
        $kmDistOri = (float) EconomicParameter::getValue('Km_Dist_ORI', 250);
        $vehicleWearRate = (float) $rateCard->vehicle_wear_usd_km;

        // Revenues
        $revOcc = round($kgOcc * $tariffOcc, 2);
        $revCen = round($kgCen * $tariffCen, 2);
        $revOri = round($kgOri * $tariffOri, 2);
        $revTotal = round($revOcc + $revCen + $revOri + $otherRevenue, 2);

        // ETAPA 1: PUERTO
        $tripsPuerto = isset($data['trips_port']) && $data['trips_port'] !== '' ? (float) $data['trips_port'] : $stdViajesPuerto;
        $portApptCost = round($tripsPuerto * $rateCard->port_appointment_usd, 2);
        $normLitersPuerto = $tripsPuerto * $litrosPuertoViaje;
        $appliedLitersPuerto = isset($data['liters_port']) && $data['liters_port'] !== '' ? (float) $data['liters_port'] : $normLitersPuerto;
        $fuelPortCost = round($appliedLitersPuerto * $dieselPrice, 2);
        $driverDietCost = isset($data['diet_port']) && $data['diet_port'] !== '' ? (float) $data['diet_port'] : round($tripsPuerto * $rateCard->driver_diet_usd, 2);
        $vehicleWearCost = round($tripsPuerto * $kmPuertoViaje * $rateCard->vehicle_wear_usd_km, 2);
        $totalPortCost = round($portApptCost + $fuelPortCost + $driverDietCost + $vehicleWearCost, 2);

        // ETAPA 2: TRANSITARIA
        $normShiftsTransitaria = $kgTotal > 0 ? (float) ceil($kgTotal / $rendDesagrupe) : 0.0;
        $appliedShiftsTransitaria = isset($data['labor_transitaria']) && $data['labor_transitaria'] !== '' ? (float) $data['labor_transitaria'] : $normShiftsTransitaria;
        $laborTransitariaCost = round($appliedShiftsTransitaria * $rateCard->labor_shift_transitaria_usd, 2);
        $serviceTransitariaCost = isset($data['service_transitaria']) && $data['service_transitaria'] !== '' ? (float) $data['service_transitaria'] : ($transitaria === 'ENCI' ? (float) $rateCard->transitaria_service_enci_usd : ($transitaria === 'A.V' ? (float) $rateCard->transitaria_service_av_usd : 0.0));
        $demurrageCost = (float) ($data['storage_transitaria'] ?? 0);
        $totalTransitariaCost = round($laborTransitariaCost + $serviceTransitariaCost + $demurrageCost, 2);

        // ETAPA 3: DISTRIBUCIÓN REGIONAL
        // Occidente
        $tripsOcc = $kgOcc > 0 ? (float) ceil($kgOcc / $capCamionOCC) : 0.0;
        $normLitersDistOcc = $tripsOcc * $lDistOCC;
        $appliedLitersDistOcc = isset($data['liters_dist_occ']) && $data['liters_dist_occ'] !== '' ? (float) $data['liters_dist_occ'] : $normLitersDistOcc;
        $fuelOccCost = round($appliedLitersDistOcc * $dieselPrice, 2);
        $useOwnOcc = !empty($data['apply_regional_vehicle_wear']) || !empty($data['use_own_truck_occ']) || isset($data['wear_dist_occ']);
        $wearOccCost = isset($data['wear_dist_occ']) && $data['wear_dist_occ'] !== ''
            ? (float) $data['wear_dist_occ']
            : ($useOwnOcc ? round($tripsOcc * $kmDistOcc * $vehicleWearRate, 2) : 0.0);
        $totalOccCost = round($fuelOccCost + $wearOccCost, 2);

        // Centro
        $tripsCen = $kgCen > 0 ? (float) ceil($kgCen / $capCamionCEN) : 0.0;
        $normLitersTrasCen = $tripsCen * $lTrasCEN;
        $appliedLitersTrasCen = isset($data['liters_tras_cen']) && $data['liters_tras_cen'] !== '' ? (float) $data['liters_tras_cen'] : $normLitersTrasCen;
        $normLitersDistCen = $tripsCen * $lDistCEN;
        $appliedLitersDistCen = isset($data['liters_dist_cen']) && $data['liters_dist_cen'] !== '' ? (float) $data['liters_dist_cen'] : $normLitersDistCen;
        $fuelCenCost = round(($appliedLitersTrasCen + $appliedLitersDistCen) * $dieselPrice, 2);
        $normShiftsCen = $kgCen > 0 ? (float) ceil($kgCen / $rendDesagrupeCT) : 0.0;
        $appliedShiftsCen = isset($data['labor_shifts_cen']) && $data['labor_shifts_cen'] !== '' ? (float) $data['labor_shifts_cen'] : $normShiftsCen;
        $laborCenCost = round($appliedShiftsCen * $rateCard->labor_shift_transitory_center_usd, 2);
        $useOwnCen = !empty($data['apply_regional_vehicle_wear']) || !empty($data['use_own_truck_cen']) || isset($data['wear_dist_cen']);
        $wearCenCost = isset($data['wear_dist_cen']) && $data['wear_dist_cen'] !== ''
            ? (float) $data['wear_dist_cen']
            : ($useOwnCen ? round($tripsCen * ($kmTrasCen + $kmDistCen) * $vehicleWearRate, 2) : 0.0);
        $totalCenCost = round($fuelCenCost + $laborCenCost + $wearCenCost, 2);

        // Oriente
        $tripsOri = $kgOri > 0 ? (float) ceil($kgOri / $capCamionORI) : 0.0;
        $normLitersTrasOri = $tripsOri * $lTrasORI;
        $appliedLitersTrasOri = isset($data['liters_tras_ori']) && $data['liters_tras_ori'] !== '' ? (float) $data['liters_tras_ori'] : $normLitersTrasOri;
        $normLitersDistOri = $tripsOri * $lDistORI;
        $appliedLitersDistOri = isset($data['liters_dist_ori']) && $data['liters_dist_ori'] !== '' ? (float) $data['liters_dist_ori'] : $normLitersDistOri;
        $fuelOriCost = round(($appliedLitersTrasOri + $appliedLitersDistOri) * $dieselPrice, 2);
        $normShiftsOri = $kgOri > 0 ? (float) ceil($kgOri / $rendDesagrupeCT) : 0.0;
        $appliedShiftsOri = isset($data['labor_shifts_ori']) && $data['labor_shifts_ori'] !== '' ? (float) $data['labor_shifts_ori'] : $normShiftsOri;
        $laborOriCost = round($appliedShiftsOri * $rateCard->labor_shift_transitory_center_usd, 2);
        $useOwnOri = !empty($data['apply_regional_vehicle_wear']) || !empty($data['use_own_truck_ori']) || isset($data['wear_dist_ori']);
        $wearOriCost = isset($data['wear_dist_ori']) && $data['wear_dist_ori'] !== ''
            ? (float) $data['wear_dist_ori']
            : ($useOwnOri ? round($tripsOri * ($kmTrasOri + $kmDistOri) * $vehicleWearRate, 2) : 0.0);
        $totalOriCost = round($fuelOriCost + $laborOriCost + $wearOriCost, 2);

        $totalDistributionCost = round($totalOccCost + $totalCenCost + $totalOriCost, 2);

        // ETAPA 4: COMISIONES Y DEDUCCIONES DIRECTAS
        if ($baseComision === '% DEL INGRESO') {
            $commOcc = round($revOcc * $rateCard->commercial_commission_pct, 2);
            $commCen = round($revCen * $rateCard->commercial_commission_pct, 2);
            $commOri = round($revOri * $rateCard->commercial_commission_pct, 2);
        } else {
            $commOcc = round($kgOcc * $rateCard->commercial_commission_usd_kg, 2);
            $commCen = round($kgCen * $rateCard->commercial_commission_usd_kg, 2);
            $commOri = round($kgOri * $rateCard->commercial_commission_usd_kg, 2);
        }
        $totalCommission = round($commOcc + $commCen + $commOri, 2);

        $costClaims = round($revTotal * $pctReclamaciones, 2);
        $costBank = round($revTotal * $pctBanco, 2);
        $totalProvisionsAndBank = round($costClaims + $costBank, 2);

        // TOTAL COSTOS VARIABLES Y MARGEN DE CONTRIBUCIÓN
        $totalVariableCosts = round(
            $totalPortCost +
            $totalTransitariaCost +
            $totalDistributionCost +
            $totalCommission +
            $totalProvisionsAndBank +
            $otherDirectCosts,
            2
        );

        $contributionMargin = round($revTotal - $totalVariableCosts, 2);
        $contributionMarginPct = $revTotal > 0 ? round(($contributionMargin / $revTotal) * 100, 2) : 0.0;
        $mcPerKg = $kgTotal > 0 ? round($contributionMargin / $kgTotal, 4) : 0.0;

        // COSTOS FIJOS Y PRORRATEO (FASE 6.2: CONCEPTOS DETALLADOS + TRIBUTOS SALARIALES)
        $arrivalCarbon = Carbon::parse($arrivalDate);
        $yearMonth = $arrivalCarbon->format('Y-m');
        $fixedCostsDetail = FixedCostItem::calculateMonthlyFixedCosts($yearMonth);
        $fixedMonthly = FixedCostMonthly::getForMonth($yearMonth);

        $generalFixedUsd = $fixedCostsDetail['general_fixed_usd'] ?? $fixedMonthly->general_fixed_usd;
        $occFixedUsd = $fixedCostsDetail['occidente_fixed_usd'] ?? $fixedMonthly->occidente_fixed_usd;
        $cenFixedUsd = $fixedCostsDetail['centro_fixed_usd'] ?? $fixedMonthly->centro_fixed_usd;
        $oriFixedUsd = $fixedCostsDetail['oriente_fixed_usd'] ?? $fixedMonthly->oriente_fixed_usd;

        $monthKg = isset($data['total_month_kg']) && (float) $data['total_month_kg'] > 0 ? (float) $data['total_month_kg'] : 19500.0; // Baseline oficial del modelo Excel
        $monthOccKg = isset($data['total_month_occ_kg']) && (float) $data['total_month_occ_kg'] > 0 ? (float) $data['total_month_occ_kg'] : 9800.0;
        $monthCenKg = isset($data['total_month_cen_kg']) && (float) $data['total_month_cen_kg'] > 0 ? (float) $data['total_month_cen_kg'] : 4700.0;
        $monthOriKg = isset($data['total_month_ori_kg']) && (float) $data['total_month_ori_kg'] > 0 ? (float) $data['total_month_ori_kg'] : 5000.0;

        $shareGeneral = $metodoProrrateo === 'KG' ? ($monthKg > 0 ? ($kgTotal / $monthKg) : 1.0) : (1.0 / max(1, (int) ($data['total_month_containers'] ?? 2)));
        $shareOcc = $monthOccKg > 0 ? ($kgOcc / $monthOccKg) : ($kgOcc > 0 ? 1.0 : 0.0);
        $shareCen = $monthCenKg > 0 ? ($kgCen / $monthCenKg) : ($kgCen > 0 ? 1.0 : 0.0);
        $shareOri = $monthOriKg > 0 ? ($kgOri / $monthOriKg) : ($kgOri > 0 ? 1.0 : 0.0);

        $fixedGeneralAssigned = round($generalFixedUsd * $shareGeneral, 2);
        $fixedOccAssigned = round($occFixedUsd * $shareOcc, 2);
        $fixedCenAssigned = round($cenFixedUsd * $shareCen, 2);
        $fixedOriAssigned = round($oriFixedUsd * $shareOri, 2);
        $totalFixedAssigned = round($fixedGeneralAssigned + $fixedOccAssigned + $fixedCenAssigned + $fixedOriAssigned, 2);

        // TRIBUTOS E IMPUESTOS
        $taxSalesServices = round($revTotal * $impVentasServ, 2);
        $taxTerritorial = round($revTotal * $contribTerritorial, 2);
        $totalTaxesOnRevenue = round($taxSalesServices + $taxTerritorial, 2);

        // RESULTADOS ANTES Y DESPUÉS DE IMPUESTOS
        $profitBeforeTax = round($contributionMargin - $totalFixedAssigned - $totalTaxesOnRevenue, 2);
        $estimatedIncomeTax = $profitBeforeTax > 0 ? round($profitBeforeTax * $impUtilidades, 2) : 0.0;
        $netProfit = round($profitBeforeTax - $estimatedIncomeTax, 2);
        $netMarginPct = $revTotal > 0 ? round(($netProfit / $revTotal) * 100, 2) : 0.0;

        $costPerKg = $kgTotal > 0 ? round(($totalVariableCosts + $totalFixedAssigned + $totalTaxesOnRevenue) / $kgTotal, 4) : 0.0;
        $revenuePerKg = $kgTotal > 0 ? round($revTotal / $kgTotal, 4) : 0.0;
        $profitBeforeTaxPerKg = $kgTotal > 0 ? round($profitBeforeTax / $kgTotal, 4) : 0.0;

        // DESGLOSE Y CUADRE REGIONAL EXACTO
        $sharedCosts = round($totalPortCost + $totalTransitariaCost + $otherDirectCosts + $fixedGeneralAssigned, 2);
        $revenueRetentionMultiplier = 1.0 - $pctReclamaciones - $pctBanco - $impVentasServ - $contribTerritorial;

        $weightRatioOcc = $kgTotal > 0 ? ($kgOcc / $kgTotal) : 0.0;
        $weightRatioCen = $kgTotal > 0 ? ($kgCen / $kgTotal) : 0.0;
        $weightRatioOri = $kgTotal > 0 ? ($kgOri / $kgTotal) : 0.0;

        $regionalOccNet = round(($revOcc + $otherRevenue * $weightRatioOcc) * $revenueRetentionMultiplier - $totalOccCost - $commOcc - $fixedOccAssigned - ($sharedCosts * $weightRatioOcc), 2);
        $regionalCenNet = round(($revCen + $otherRevenue * $weightRatioCen) * $revenueRetentionMultiplier - $totalCenCost - $commCen - $fixedCenAssigned - ($sharedCosts * $weightRatioCen), 2);
        $regionalOriNet = round(($revOri + $otherRevenue * $weightRatioOri) * $revenueRetentionMultiplier - $totalOriCost - $commOri - $fixedOriAssigned - ($sharedCosts * $weightRatioOri), 2);
        $reconciliationDiff = round($profitBeforeTax - ($regionalOccNet + $regionalCenNet + $regionalOriNet), 2);

        // TARIFAS MÍNIMAS DE EQUILIBRIO Y PUNTO DE EQUILIBRIO + MARGEN DE SEGURIDAD (FASE 6.2.9)
        $breakevenTariff = ($kgTotal > 0 && $revenueRetentionMultiplier > 0)
            ? round(($totalVariableCosts + $totalFixedAssigned) / ($kgTotal * $revenueRetentionMultiplier), 4)
            : 0.0;

        $breakevenKg = ($mcPerKg > 0) ? round(($totalFixedAssigned + $totalTaxesOnRevenue) / $mcPerKg, 2) : 0.0;
        $safetyMarginKg = round($kgTotal - $breakevenKg, 2);
        $safetyMarginPct = $kgTotal > 0 ? round(($safetyMarginKg / $kgTotal) * 100, 2) : 0.0;

        // VEREDICTO DE TOMA DE DECISIONES (FASE 5.3)
        $verdict = 'FACTIBLE';
        $summary = '';

        if ($profitBeforeTax < 0) {
            $verdict = 'NO_FACTIBLE';
            $deficit = abs($profitBeforeTax);
            $kgNeeded = $mcPerKg > 0 ? round($deficit / $mcPerKg, 0) : 0;
            $summary = "🔴 Contenedor Antieconómico: Genera pérdida estimada de -\${$deficit} USD. Para alcanzar el punto de equilibrio se requiere transportar al menos {$kgNeeded} kg adicionales con la tarifa vigente.";
        } elseif ($netMarginPct < 8.0) {
            $verdict = 'MARGINAL';
            $summary = "🟡 Rentabilidad Marginal: Utilidad neta de \${$netProfit} USD con margen de {$netMarginPct}%. El envío es vulnerable a subidas del diésel o desviaciones operativas en ruta.";
        } else {
            $verdict = 'FACTIBLE';
            $summary = "🟢 Operación Altamente Factible: Utilidad neta proyectada de \${$netProfit} USD ({$netMarginPct}% margen neto) y Margen de Contribución de \${$contributionMargin} USD ({$contributionMarginPct}%).";
        }

        // ALERTAS OPERATIVAS (Consumo, desvío, fecha)
        $alerts = [];
        $totalNormLiters = $normLitersPuerto + $normLitersDistOcc + $normLitersTrasCen + $normLitersDistCen + $normLitersTrasOri + $normLitersDistOri;
        $totalAppliedLiters = $appliedLitersPuerto + $appliedLitersDistOcc + $appliedLitersTrasCen + $appliedLitersDistCen + $appliedLitersTrasOri + $appliedLitersDistOri;
        $fuelDeviationPct = $totalNormLiters > 0 ? round((($totalAppliedLiters / $totalNormLiters) - 1.0) * 100, 1) : 0.0;

        if ($normLitersPuerto > 0 && ($appliedLitersPuerto / $normLitersPuerto - 1.0) > $toleranciaComb) {
            $alerts[] = 'Combustible al puerto supera la norma técnica en más de ' . round(($appliedLitersPuerto / $normLitersPuerto - 1.0) * 100, 1) . '%.';
        }
        if ($profitBeforeTax < 0) {
            $alerts[] = 'Alerta crítica: Envío con pérdida neta antes de impuestos.';
        }
        if ($regionalOriNet < 0) {
            $alerts[] = 'Alerta regional: El tramo Oriente genera déficit individual de ' . abs($regionalOriNet) . ' USD.';
        }

        return [
            'container_id' => $containerId,
            'arrival_date' => $arrivalDate,
            'transitaria' => $transitaria,
            'status' => $status,
            'weights' => [
                'kg_occidente' => $kgOcc,
                'kg_centro' => $kgCen,
                'kg_oriente' => $kgOri,
                'kg_total' => $kgTotal,
            ],
            'revenues' => [
                'occidente' => $revOcc,
                'centro' => $revCen,
                'oriente' => $revOri,
                'other' => $otherRevenue,
                'total' => $revTotal,
                'revenue_per_kg' => $revenuePerKg,
            ],
            'costs_breakdown' => [
                'port' => [
                    'trips' => $tripsPuerto,
                    'appointment_cost' => $portApptCost,
                    'liters_norm' => $normLitersPuerto,
                    'liters_applied' => $appliedLitersPuerto,
                    'fuel_cost' => $fuelPortCost,
                    'diet_cost' => $driverDietCost,
                    'vehicle_wear_cost' => $vehicleWearCost,
                    'total' => $totalPortCost,
                ],
                'transitaria' => [
                    'shifts_norm' => $normShiftsTransitaria,
                    'shifts_applied' => $appliedShiftsTransitaria,
                    'labor_cost' => $laborTransitariaCost,
                    'service_cost' => $serviceTransitariaCost,
                    'demurrage_cost' => $demurrageCost,
                    'total' => $totalTransitariaCost,
                ],
                'distribution' => [
                    'occidente' => [
                        'trips' => $tripsOcc,
                        'liters_norm' => $normLitersDistOcc,
                        'liters_applied' => $appliedLitersDistOcc,
                        'fuel_cost' => $fuelOccCost,
                        'vehicle_wear_cost' => $wearOccCost,
                        'total' => $totalOccCost,
                    ],
                    'centro' => [
                        'trips' => $tripsCen,
                        'liters_tras_norm' => $normLitersTrasCen,
                        'liters_tras_applied' => $appliedLitersTrasCen,
                        'liters_dist_norm' => $normLitersDistCen,
                        'liters_dist_applied' => $appliedLitersDistCen,
                        'fuel_cost' => $fuelCenCost,
                        'shifts_applied' => $appliedShiftsCen,
                        'labor_cost' => $laborCenCost,
                        'vehicle_wear_cost' => $wearCenCost,
                        'total' => $totalCenCost,
                    ],
                    'oriente' => [
                        'trips' => $tripsOri,
                        'liters_tras_norm' => $normLitersTrasOri,
                        'liters_tras_applied' => $appliedLitersTrasOri,
                        'liters_dist_norm' => $normLitersDistOri,
                        'liters_dist_applied' => $appliedLitersDistOri,
                        'fuel_cost' => $fuelOriCost,
                        'shifts_applied' => $appliedShiftsOri,
                        'labor_cost' => $laborOriCost,
                        'vehicle_wear_cost' => $wearOriCost,
                        'total' => $totalOriCost,
                    ],
                    'total_vehicle_wear' => round($wearOccCost + $wearCenCost + $wearOriCost, 2),
                    'total' => $totalDistributionCost,
                ],
                'commissions' => [
                    'occidente' => $commOcc,
                    'centro' => $commCen,
                    'oriente' => $commOri,
                    'total' => $totalCommission,
                ],
                'provisions_and_bank' => [
                    'claims' => $costClaims,
                    'bank' => $costBank,
                    'total' => $totalProvisionsAndBank,
                ],
                'other_direct' => $otherDirectCosts,
                'total_variable' => $totalVariableCosts,
            ],
            'contribution_margin' => [
                'amount' => $contributionMargin,
                'pct' => $contributionMarginPct,
                'per_kg' => $mcPerKg,
            ],
            'fixed_costs_assigned' => [
                'general' => $fixedGeneralAssigned,
                'occidente' => $fixedOccAssigned,
                'centro' => $fixedCenAssigned,
                'oriente' => $fixedOriAssigned,
                'total' => $totalFixedAssigned,
                'detailed_concepts' => $fixedCostsDetail['breakdown_by_item'] ?? [],
                'salary_bases' => $fixedCostsDetail['salary_bases'] ?? [],
                'social_security_taxes' => $fixedCostsDetail['social_security_taxes'] ?? [],
                'labor_force_taxes' => $fixedCostsDetail['labor_force_taxes'] ?? [],
                'total_month_fixed' => $fixedCostsDetail['total_fixed_usd'] ?? $fixedMonthly->total_fixed_usd,
            ],
            'taxes' => [
                'sales_and_services' => $taxSalesServices,
                'territorial_contribution' => $taxTerritorial,
                'total_on_revenue' => $totalTaxesOnRevenue,
                'estimated_income_tax' => $estimatedIncomeTax,
            ],
            'net_results' => [
                'profit_before_tax' => $profitBeforeTax,
                'net_profit' => $netProfit,
                'net_margin_pct' => $netMarginPct,
                'cost_per_kg' => $costPerKg,
                'profit_before_tax_per_kg' => $profitBeforeTaxPerKg,
            ],
            'regional_breakdown' => [
                'occidente' => [
                    'kg' => $kgOcc,
                    'revenue' => $revOcc,
                    'direct_cost' => $totalOccCost + $commOcc + $fixedOccAssigned,
                    'net_profit' => $regionalOccNet,
                ],
                'centro' => [
                    'kg' => $kgCen,
                    'revenue' => $revCen,
                    'direct_cost' => $totalCenCost + $commCen + $fixedCenAssigned,
                    'net_profit' => $regionalCenNet,
                ],
                'oriente' => [
                    'kg' => $kgOri,
                    'revenue' => $revOri,
                    'direct_cost' => $totalOriCost + $commOri + $fixedOriAssigned,
                    'net_profit' => $regionalOriNet,
                ],
                'reconciliation_diff' => $reconciliationDiff,
            ],
            'breakeven' => [
                'breakeven_tariff_usd_kg' => $breakevenTariff,
                'breakeven_kg' => $breakevenKg,
                'safety_margin_kg' => $safetyMarginKg,
                'safety_margin_pct' => $safetyMarginPct,
            ],
            'fuel_telemetry' => [
                'total_norm_liters' => $totalNormLiters,
                'total_applied_liters' => $totalAppliedLiters,
                'deviation_pct' => $fuelDeviationPct,
                'diesel_price' => $dieselPrice,
            ],
            'decision' => [
                'verdict' => $verdict,
                'summary' => $summary,
            ],
            'alerts' => $alerts,
        ];
    }

    /**
     * Calculate and persist container profitability analysis record.
     */
    public function calculateAndSave(array $data, ?int $userId = null): ContainerProfitabilityAnalysis
    {
        $res = $this->calculate($data);

        $analysis = ContainerProfitabilityAnalysis::create([
            'container_id' => $res['container_id'],
            'arrival_date' => $res['arrival_date'],
            'transitaria' => $res['transitaria'],
            'status' => $res['status'],
            'kg_occidente' => $res['weights']['kg_occidente'],
            'kg_centro' => $res['weights']['kg_centro'],
            'kg_oriente' => $res['weights']['kg_oriente'],
            'kg_total' => $res['weights']['kg_total'],
            'revenue_occidente' => $res['revenues']['occidente'],
            'revenue_centro' => $res['revenues']['centro'],
            'revenue_oriente' => $res['revenues']['oriente'],
            'revenue_other' => $res['revenues']['other'],
            'revenue_total' => $res['revenues']['total'],
            'cost_port_total' => $res['costs_breakdown']['port']['total'],
            'cost_transitaria_total' => $res['costs_breakdown']['transitaria']['total'],
            'cost_distribution_occidente' => $res['costs_breakdown']['distribution']['occidente']['total'],
            'cost_distribution_centro' => $res['costs_breakdown']['distribution']['centro']['total'],
            'cost_distribution_oriente' => $res['costs_breakdown']['distribution']['oriente']['total'],
            'cost_distribution_total' => $res['costs_breakdown']['distribution']['total'],
            'cost_commission_total' => $res['costs_breakdown']['commissions']['total'],
            'cost_provisions_and_bank' => $res['costs_breakdown']['provisions_and_bank']['total'],
            'cost_variable_total' => $res['costs_breakdown']['total_variable'],
            'contribution_margin' => $res['contribution_margin']['amount'],
            'contribution_margin_pct' => $res['contribution_margin']['pct'],
            'fixed_costs_assigned_total' => $res['fixed_costs_assigned']['total'],
            'taxes_sales_and_services' => $res['taxes']['sales_and_services'],
            'tax_territorial_contribution' => $res['taxes']['territorial_contribution'],
            'profit_before_tax' => $res['net_results']['profit_before_tax'],
            'estimated_income_tax' => $res['taxes']['estimated_income_tax'],
            'net_profit' => $res['net_results']['net_profit'],
            'net_margin_pct' => $res['net_results']['net_margin_pct'],
            'cost_per_kg' => $res['net_results']['cost_per_kg'],
            'revenue_per_kg' => $res['revenues']['revenue_per_kg'],
            'profit_before_tax_per_kg' => $res['net_results']['profit_before_tax_per_kg'],
            'breakeven_kg' => $res['breakeven']['breakeven_kg'],
            'breakeven_tariff_usd_kg' => $res['breakeven']['breakeven_tariff_usd_kg'],
            'decision_verdict' => $res['decision']['verdict'],
            'decision_summary' => $res['decision']['summary'],
            'regional_breakdown' => $res['regional_breakdown'],
            'applied_parameters' => $res['fuel_telemetry'],
            'created_by' => $userId,
        ]);

        $this->generateAndPersistAlerts($analysis, $res);

        return $analysis;
    }

    /**
     * Evalúa y persiste alertas económicas basadas en semáforo de riesgo (Fase 6.5.10).
     */
    public function generateAndPersistAlerts(ContainerProfitabilityAnalysis $analysis, array $calcResult): void
    {
        $profitBeforeTax = (float) ($calcResult['net_results']['profit_before_tax'] ?? 0);
        $netMarginPct = (float) ($calcResult['net_results']['net_margin_pct'] ?? 0);
        $verdict = (string) ($calcResult['decision']['verdict'] ?? '');
        $fuelDeviationPct = (float) ($calcResult['fuel_telemetry']['deviation_pct'] ?? 0);

        // 1. Alerta crítica: Margen negativo / Déficit general
        if ($profitBeforeTax < 0) {
            EconomicAlert::firstOrCreate([
                'container_profitability_analysis_id' => $analysis->id,
                'alert_type' => 'MARGEN_NEGATIVO',
            ], [
                'severity' => 'alta',
                'description' => 'El contenedor genera una pérdida neta antes de impuestos de -$' . number_format(abs($profitBeforeTax), 2) . ' USD.',
                'is_investigated' => false,
            ]);
        }

        // 2. Alerta media: Rentabilidad marginal (< 8% de margen neto)
        if ($verdict === 'MARGINAL') {
            EconomicAlert::firstOrCreate([
                'container_profitability_analysis_id' => $analysis->id,
                'alert_type' => 'RENTABILIDAD_MARGINAL',
            ], [
                'severity' => 'media',
                'description' => "Margen neto crítico de {$netMarginPct}%, inferior al umbral mínimo de seguridad del 8.0%.",
                'is_investigated' => false,
            ]);
        }

        // 3. Alerta media: Desvío significativo de combustible (> 10% de la norma técnica)
        if ($fuelDeviationPct > 10.0) {
            EconomicAlert::firstOrCreate([
                'container_profitability_analysis_id' => $analysis->id,
                'alert_type' => 'DESVIO_COMBUSTIBLE',
            ], [
                'severity' => 'media',
                'description' => "El consumo de combustible diésel supera la norma técnica en un {$fuelDeviationPct}%.",
                'is_investigated' => false,
            ]);
        }

        // 4. Alerta media/baja: Déficit individual en tramos regionales
        $regOcc = (float) ($calcResult['regional_breakdown']['occidente']['net_profit'] ?? 0);
        $regCen = (float) ($calcResult['regional_breakdown']['centro']['net_profit'] ?? 0);
        $regOri = (float) ($calcResult['regional_breakdown']['oriente']['net_profit'] ?? 0);

        if ($regOcc < 0 || $regCen < 0 || $regOri < 0) {
            $deficits = [];
            if ($regOcc < 0) $deficits[] = "Occidente (-$" . abs($regOcc) . " USD)";
            if ($regCen < 0) $deficits[] = "Centro (-$" . abs($regCen) . " USD)";
            if ($regOri < 0) $deficits[] = "Oriente (-$" . abs($regOri) . " USD)";

            EconomicAlert::firstOrCreate([
                'container_profitability_analysis_id' => $analysis->id,
                'alert_type' => 'DEFICIT_REGIONAL',
            ], [
                'severity' => 'media',
                'description' => 'Déficit directo en tramos regionales: ' . implode(', ', $deficits) . '.',
                'is_investigated' => false,
            ]);
        }
    }

    /**
     * Evaluate economic viability of a regional dispatch route prior to vehicle departure.
     */
    public function evaluateRouteViability(RegionalDispatchRoute $route, ?float $customDieselPrice = null): array
    {
        $rateCard = FuelAndTariffRate::getRateForDate();
        $dieselPrice = $customDieselPrice ?? ($rateCard ? (float) $rateCard->diesel_price_usd_l : 2.0000);

        // Calculate total weight of assigned equipments
        $totalRouteKg = 0.0;
        $totalEstimatedTariff = 0.0;

        foreach ($route->equipments as $equipment) {
            // Standard weight assumption per equipment type if not explicitly stored
            $weight = match ($equipment->type) {
                'VEHICLE' => 120.0,
                'SOLAR_SYSTEM' => 85.0,
                'BATTERY_PACK' => 45.0,
                default => 25.0,
            };
            $totalRouteKg += $weight;
            $totalEstimatedTariff += ($weight * 0.80); // 0.80 USD/kg
        }

        // Determine destination zone consumption norm
        $destinationWarehouse = $route->destinationWarehouse;
        $zone = 'OCCIDENTE';
        $fuelNormLiters = 120.0;

        if ($destinationWarehouse) {
            $name = strtoupper($destinationWarehouse->name);
            if (str_contains($name, 'CENTRO') || str_contains($name, 'SANTA CLARA') || str_contains($name, 'CIENFUEGOS')) {
                $zone = 'CENTRO';
                $fuelNormLiters = 400.0; // 250 traslado + 150 reparto
            } elseif (str_contains($name, 'ORIENTE') || str_contains($name, 'SANTIAGO') || str_contains($name, 'HOLGUIN')) {
                $zone = 'ORIENTE';
                $fuelNormLiters = 630.0; // 450 traslado + 180 reparto
            }
        }

        $estimatedFuelCost = round($fuelNormLiters * $dieselPrice, 2);
        $driverDietCost = round(($rateCard ? (float) $rateCard->driver_diet_usd : 10.00) * ($zone === 'ORIENTE' ? 3 : ($zone === 'CENTRO' ? 2 : 1)), 2);
        $totalTripCost = round($estimatedFuelCost + $driverDietCost, 2);

        $netFreightContribution = round($totalEstimatedTariff - $totalTripCost, 2);
        $minKgRequired = round($totalTripCost / 0.80, 0);

        if ($netFreightContribution < 0) {
            $verdict = 'NO_FACTIBLE';
            $summary = "🔴 Convoy Antieconómico: El flete estimado (\${$totalEstimatedTariff} USD) no cubre el combustible y dietas del viaje (\${$totalTripCost} USD). Carga actual: {$totalRouteKg} kg. Mínimo requerido para salir: {$minKgRequired} kg.";
        } elseif ($netFreightContribution < 100.0) {
            $verdict = 'MARGINAL';
            $summary = "🟡 Convoy Marginal: Contribución neta de flete reducida (\${$netFreightContribution} USD). Se recomienda agrupar más equipos antes del despacho.";
        } else {
            $verdict = 'FACTIBLE';
            $summary = "🟢 Convoy Viable: Flete de \${$totalEstimatedTariff} USD cubre holgadamente los costos del trayecto (\${$totalTripCost} USD) generando \${$netFreightContribution} USD de contribución.";
        }

        return [
            'route_id' => $route->id,
            'route_code' => $route->route_code,
            'zone' => $zone,
            'total_kg' => $totalRouteKg,
            'equipments_count' => $route->equipments->count(),
            'fuel_norm_liters' => $fuelNormLiters,
            'diesel_price' => $dieselPrice,
            'estimated_fuel_cost' => $estimatedFuelCost,
            'driver_diet_cost' => $driverDietCost,
            'total_trip_cost' => $totalTripCost,
            'estimated_freight_revenue' => $totalEstimatedTariff,
            'net_freight_contribution' => $netFreightContribution,
            'min_kg_required' => $minKgRequired,
            'verdict' => $verdict,
            'summary' => $summary,
        ];
    }

    /**
     * Run a multi-variable sensitivity matrix across diesel price and freight tariff rates.
     */
    public function simulateSensitivity(array $baseData, array $dieselPrices = [1.50, 2.00, 2.50, 3.00], array $tariffs = [0.70, 0.80, 0.90, 1.00]): array
    {
        $matrix = [];

        foreach ($tariffs as $tariff) {
            $row = ['tariff' => $tariff, 'results' => []];
            foreach ($dieselPrices as $diesel) {
                $simData = array_merge($baseData, [
                    'custom_tariff_rate' => $tariff,
                    'custom_diesel_price' => $diesel,
                ]);
                $calc = $this->calculate($simData);
                $row['results'][(string) $diesel] = [
                    'net_profit' => $calc['net_results']['net_profit'],
                    'net_margin_pct' => $calc['net_results']['net_margin_pct'],
                    'verdict' => $calc['decision']['verdict'],
                ];
            }
            $matrix[] = $row;
        }

        return [
            'diesel_prices' => $dieselPrices,
            'tariffs' => $tariffs,
            'matrix' => $matrix,
        ];
    }

    /**
     * Calcula los gastos fijos no absorbidos del mes (Fase 6.2.5).
     * Diferencia entre el total de costos fijos del mes y la suma asignada a los contenedores procesados.
     *
     * @return array{
     *     year_month: string,
     *     total_month_fixed: float,
     *     total_assigned_fixed: float,
     *     unabsorbed_fixed: float,
     *     absorption_rate_pct: float,
     *     centers: array<string, array{total: float, assigned: float, unabsorbed: float}>
     * }
     */
    public function calculateUnabsorbedFixedCosts(string $yearMonth): array
    {
        $fixedCostsDetail = FixedCostItem::calculateMonthlyFixedCosts($yearMonth);
        $totalGeneral = (float) $fixedCostsDetail['general_fixed_usd'];
        $totalOcc = (float) $fixedCostsDetail['occidente_fixed_usd'];
        $totalCen = (float) $fixedCostsDetail['centro_fixed_usd'];
        $totalOri = (float) $fixedCostsDetail['oriente_fixed_usd'];
        $totalMonth = (float) $fixedCostsDetail['total_fixed_usd'];

        // Obtener análisis de contenedores que arribaron en ese mes
        $analyses = ContainerProfitabilityAnalysis::where('arrival_date', 'like', "{$yearMonth}%")->get();

        $assignedGeneral = 0.0;
        $assignedOcc = 0.0;
        $assignedCen = 0.0;
        $assignedOri = 0.0;

        foreach ($analyses as $analysis) {
            $breakdown = $analysis->applied_rates_snapshot['fixed_costs_assigned'] ?? null;
            if ($breakdown) {
                $assignedGeneral += (float) ($breakdown['general'] ?? 0.0);
                $assignedOcc += (float) ($breakdown['occidente'] ?? 0.0);
                $assignedCen += (float) ($breakdown['centro'] ?? 0.0);
                $assignedOri += (float) ($breakdown['oriente'] ?? 0.0);
            } else {
                $assignedGeneral += (float) $analysis->fixed_costs_assigned_total;
            }
        }

        $assignedGeneral = round($assignedGeneral, 2);
        $assignedOcc = round($assignedOcc, 2);
        $assignedCen = round($assignedCen, 2);
        $assignedOri = round($assignedOri, 2);
        $totalAssigned = round($assignedGeneral + $assignedOcc + $assignedCen + $assignedOri, 2);

        $unabsorbedGeneral = round($totalGeneral - $assignedGeneral, 2);
        $unabsorbedOcc = round($totalOcc - $assignedOcc, 2);
        $unabsorbedCen = round($totalCen - $assignedCen, 2);
        $unabsorbedOri = round($totalOri - $assignedOri, 2);
        $unabsorbedTotal = round($totalMonth - $totalAssigned, 2);

        $absorptionRatePct = $totalMonth > 0 ? round(($totalAssigned / $totalMonth) * 100, 2) : 0.0;

        return [
            'year_month' => $yearMonth,
            'total_month_fixed' => $totalMonth,
            'total_assigned_fixed' => $totalAssigned,
            'unabsorbed_fixed' => $unabsorbedTotal,
            'absorption_rate_pct' => $absorptionRatePct,
            'centers' => [
                'general' => ['total' => $totalGeneral, 'assigned' => $assignedGeneral, 'unabsorbed' => $unabsorbedGeneral],
                'occidente' => ['total' => $totalOcc, 'assigned' => $assignedOcc, 'unabsorbed' => $unabsorbedOcc],
                'centro' => ['total' => $totalCen, 'assigned' => $assignedCen, 'unabsorbed' => $unabsorbedCen],
                'oriente' => ['total' => $totalOri, 'assigned' => $assignedOri, 'unabsorbed' => $unabsorbedOri],
            ],
        ];
    }

    /**
     * Cierra económicamente un análisis congelando un snapshot completo de tarifas y parámetros (Fase 6.2.8).
     */
    public function closeEconomicAnalysis(
        ContainerProfitabilityAnalysis $analysis,
        User $user,
        ?string $notes = null
    ): ContainerProfitabilityAnalysis {
        $dateStr = $analysis->arrival_date ? $analysis->arrival_date->toDateString() : now()->toDateString();
        $rates = FuelAndTariffRate::getEffectiveRates($dateStr);
        $params = EconomicParameter::all()->pluck('value', 'code')->toArray();
        $yearMonth = $analysis->arrival_date ? $analysis->arrival_date->format('Y-m') : now()->format('Y-m');
        $fixedCosts = FixedCostItem::calculateMonthlyFixedCosts($yearMonth);

        $snapshot = [
            'closed_at' => now()->toIso8601String(),
            'closed_by' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value ?? (string) $user->role,
            ],
            'rates' => $rates->toArray(),
            'parameters' => $params,
            'fixed_costs_assigned' => [
                'general' => $analysis->applied_parameters['fixed_costs_assigned']['general'] ?? 0.0,
                'occidente' => $analysis->applied_parameters['fixed_costs_assigned']['occidente'] ?? 0.0,
                'centro' => $analysis->applied_parameters['fixed_costs_assigned']['centro'] ?? 0.0,
                'oriente' => $analysis->applied_parameters['fixed_costs_assigned']['oriente'] ?? 0.0,
                'total' => $analysis->fixed_costs_assigned_total,
            ],
            'fixed_costs_detail' => $fixedCosts,
            'notes' => $notes,
        ];

        $analysis->economic_status = 'cerrado';
        $analysis->closed_by = $user->id;
        $analysis->closed_at = now();
        $analysis->applied_rates_snapshot = $snapshot;
        $analysis->save();

        EconomicAuditLog::record(
            action: 'close_analysis',
            userId: $user->id,
            auditableType: ContainerProfitabilityAnalysis::class,
            auditableId: (string) $analysis->id,
            oldValues: ['economic_status' => 'abierto'],
            newValues: [
                'economic_status' => 'cerrado',
                'closed_at' => $analysis->closed_at->toIso8601String(),
                'applied_rates_snapshot' => $snapshot,
            ],
            ipAddress: request()?->ip(),
            userAgent: request()?->userAgent()
        );

        return $analysis;
    }

    /**
     * Reabre un análisis económico cerrado con justificación obligatoria y registro de auditoría.
     */
    public function reopenEconomicAnalysis(
        ContainerProfitabilityAnalysis $analysis,
        User $user,
        string $reason
    ): ContainerProfitabilityAnalysis {
        $oldClosedBy = $analysis->closed_by;
        $oldClosedAt = $analysis->closed_at;

        $analysis->economic_status = 'en_revision';
        $analysis->reopened_reason = $reason;
        $analysis->save();

        EconomicAuditLog::record(
            action: 'reopen_analysis',
            userId: $user->id,
            auditableType: ContainerProfitabilityAnalysis::class,
            auditableId: (string) $analysis->id,
            oldValues: [
                'economic_status' => 'cerrado',
                'closed_by' => $oldClosedBy,
                'closed_at' => $oldClosedAt?->toIso8601String(),
            ],
            newValues: [
                'economic_status' => 'en_revision',
                'reopened_reason' => $reason,
                'reopened_by' => $user->id,
                'reopened_at' => now()->toIso8601String(),
            ],
            ipAddress: request()?->ip(),
            userAgent: request()?->userAgent()
        );

        return $analysis;
    }
}
