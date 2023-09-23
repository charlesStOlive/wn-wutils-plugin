<?php

namespace Waka\Wutils\Classes;

use Illuminate\Support\Arr;

class PermissionsChecker
{
    protected $codes = [];
    protected $rules = [];
    protected $keyedCodes = [];



    public function getInfo($key = null)
    {
        if ($key) {
            return $this->{$key};
        } else {
            return [
                'codes' => $this->codes,
                'keyedCodes' => $this->keyedCodes,
                'rules' => $this->rules,
            ];
        }
    }

    public function setRules($rules)
    {
        $this->rules = $this->processElement($rules);;
    }

    public function mergeRules($rules)
    {
        $rules = $this->processElement($rules);
        $this->rules = array_merge($this->rules, $rules);
    }

    public function removeRules($rules)
    {
        $rules = $this->processElement($rules);
        $this->rules =  array_diff($this->rules, $rules);
    }
    //

    public function setCodes($codes)
    {
        $this->codes = $this->processElement($codes);
    }

    public function mergeCodes($codes)
    {
        $codes = $this->processElement($codes);
        $this->codes = array_merge($this->codes, $codes);
    }

    public function removeCodes($codes)
    {
        $codes = $this->processElement($codes);
        $this->codes =  array_diff($this->codes, $codes);
    }
    //

    /**
     * 
     */
    public function setKeyedCodes($keyedCodes)
    {
        $this->keyedCodes = $keyedCodes;
    }

    public function mergeKeyedRules($keyedCodes)
    {
        $this->keyedCodes = array_merge($this->keyedCodes, $keyedCodes);
    }

    public function removeKeyedRules($keyedCodes)
    {
        foreach ($keyedCodes as $key => $value) {
            unset($this->keyedCodes[$key]);
        }
    }

    public function check()
    {
        $results = [];
        foreach ($this->rules as $rule) {
            foreach ($this->codes as $code) {
                // $codeDoted = $this->dotArmonisation($code);
                $checked = \Str::is($rule, $code);
                //trace_log("check : " . $rule . ' : ' . $code . ' : ' . $checked);
                if ($checked) {
                    $results[] = $code;
                }
            }
        }
        return $results;
    }

    public function checkKeyedCodes()
    {
        $results = [];
        foreach ($this->rules as $rule) {
            foreach ($this->keyedCodes as $key => $value) {
                // $codeDoted = $this->dotArmonisation($key);
                $checked = \Str::is($rule, $key);
                //trace_log("check : " . $rule . ' : ' . $key . ' : ' . $checked);
                if ($checked) {
                    $results[$key] = $value;
                }
            }
        }
        //trace_log($results);
        return $results;
    }

    public function hasPermissions($codes, $rules)
    {
        (bool) count($this->check($codes, $rules));
    }

    // protected function dotArmonisation($data)
    // {
    //     $data = str_replace('::', '.', $data);
    //     return $data;
    // }

    protected function processElement($element)
    {
        if (!$element) return [];
        $finalElement = [];
        if (!is_array($element)) {
            $element = explode(',', $element);
        };
        foreach ($element as $subElement) {
            if (str_contains($subElement, ',')) {
                $subElement = $this->processElement($subElement);
                $finalElement = array_merge($finalElement, $subElement);
            } else {
                if ($subElement) array_push($finalElement, trim($subElement));
            }
        }
        return $finalElement;
    }
}
