<?php
class LabAI {
    // 1. Smart Diagnostic Logic
    public static function analyzeResult($param, $value) {
        $val = floatval($value);
        $param = strtolower($param);

        if (strpos($param, 'malaria') !== false) {
            if (stripos($value, 'positive') !== false || stripos($value, 'found') !== false) {
                return ["danger", "High Parasitemia detected. Immediate clinical intervention required."];
            }
        }
        if (strpos($param, 'hb') !== false || strpos($param, 'hemoglobin') !== false) {
            if ($val < 7) return ["danger", "Critical Anemia levels. Blood transfusion may be necessary."];
            if ($val < 11) return ["warning", "Moderate Anemia. Suggest nutritional iron support."];
        }
        return ["success", "Result falls within standard clinical thresholds."];
    }

    // 2. Predictive Inventory Logic
    public static function predictStockDepletion($current_qty, $avg_usage_per_day) {
        if ($avg_usage_per_day <= 0) return "Stable";
        $days_left = floor($current_qty / $avg_usage_per_day);
        
        if ($days_left <= 3) return "Critical: Depletion in $days_left days";
        if ($days_left <= 7) return "Warning: Restock within $days_left days";
        return "Optimal: $days_left days remaining";
    }
}
?>