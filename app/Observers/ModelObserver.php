<?php

namespace App\Observers;

class ModelObserver
{

    public function Created($model)
    {
        $id = getTypeID($model);
        $class_name = $model->getMorphClass();
        $class_name_base = class_basename($class_name);

        // check for user
        $current_user = getCurrentUser();
        if (!$current_user) {
            return;
        }

        $action = "create";

        \App\Models\HistoryAction::create([
            "model_type" => $class_name,
            "model_id" => $id,
            "archive_raw_before" => null,
            "archive_raw_after" => json_encode($model->getAttributes()),
            "changes" => null,
            "description" => ucfirst($action) . " {$class_name_base}",
            "action" => $action,
            "by" => getTypeID($current_user),
            "by_raw" => getTypeFullname($current_user),
        ]);
    }

    public function Updating($model)
    {
        $id = getTypeID($model);
        $class_name = $model->getMorphClass();
        $class_name_base = class_basename($class_name);

        // check for user
        $current_user = getCurrentUser();
        if (!$current_user) {
            return;
        }

        $tmpModel = call_user_func(getFullNamespaceByModel($class_name_base, "findOrFail"), $id);
        $date_updated_field_name = "updated_at";

        $changes = $model->getDirty();

        if ($changes) {
            if (!in_array($date_updated_field_name, array_keys($changes))) {
                $changes[$date_updated_field_name] = getDateByUnixTime($GLOBALS['dateFormat']);
                $model[$date_updated_field_name] = $changes[$date_updated_field_name];
            }
        }

        $field_keys = array_keys($changes);



        $action = "update";

        \App\Models\HistoryAction::create([
            "model_type" => $class_name,
            "model_id" => $id,
            "archive_raw_before" => json_encode($tmpModel->getAttributes()),
            "archive_raw_after" => json_encode($model->getAttributes()),
            "changes" => json_encode($field_keys),
            "description" => ucfirst($action) . " {$class_name_base} changes [" . join(", ", $field_keys) . "]",
            "action" => $action,
            "by" => getTypeID($current_user),
            "by_raw" => getTypeFullname($current_user),
        ]);
    }

    public function Deleted($model)
    {
        $id = getTypeID($model);
        $class_name = $model->getMorphClass();
        $class_name_base = class_basename($class_name);

        // check for user
        $current_user = getCurrentUser();
        if (!$current_user) {
            return;
        }

        $action = "delete";

        \App\Models\HistoryAction::create([
            "model_type" => $class_name,
            "model_id" => $id,
            "archive_raw_before" => json_encode($model->getAttributes()),
            "archive_raw_after" => null,
            "changes" => null,
            "description" => ucfirst($action) . " {$class_name_base}",
            "action" => $action,
            "by" => getTypeID($current_user),
            "by_raw" => getTypeFullname($current_user),
        ]);
    }
}
