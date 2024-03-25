<?php

namespace App\Models;

trait Features
{

    public function setStatus($status)
    {
        return $this->update([
            "status" => $status
        ]);
    }

    public function updateGrouply($list, $cbk = "updateOptionByKey")
    {
        // when (all) elements get true value return true value
        foreach ($list as $key => $item) {
            $res = $cbk($key, $item);

            if (!$res) return false;
        }

        return $res;
    }
}
