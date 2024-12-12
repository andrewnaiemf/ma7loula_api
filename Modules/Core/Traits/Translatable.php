<?php

namespace Modules\Core\Traits;

use App\Models\Translation;

trait Translatable
{
    public function loadTranslations(){
        if(app()->getLocale() != 'ar' && isset($this->translatable) && is_array($this->translatable) && count($this->translatable) > 0){
            $locale = app()->getLocale();
            $this->with = array_merge(['translations' => function($q) use ($locale){
                $q->where('lang', $locale);
            }], $this->with);
        }
    }

    public function getAttributeValue($key)
    {
        if (app()->getLocale() != 'ar' && isset($this->translatable) && is_array($this->translatable) && count($this->translatable) > 0 && in_array($key, $this->translatable)) {
            $translation = $this->translations->where('key', $key)->where('lang', app()->getLocale())->first();
            if ($translation) return $translation->value;
        }
        return parent::getAttributeValue($key);
    }

    public function saveTranslations($trans)
    {

        $data = [];
        foreach ($trans as $locale => $row) {
            foreach ($row as $key => $val) {
                if (!$val || !$key)
                    continue;
                $data[] = [
                    'translatable_type' => class_basename(__CLASS__),
                    'translatable_id' => $this->id,
                    'lang' => $locale,
                    'key' => $key,
                    'value' => $val
                ];
            }
        }
        Translation::insert($data);
    }

    public function updateTranslations($trans)
    {
        foreach ($trans as $locale => $row) {
            foreach ($row as $key => $val) {
                if (!$val || !$key)
                    continue;
                Translation::updateOrCreate(
                    [
                        'translatable_type' => class_basename(__CLASS__),
                        'translatable_id' => $this->id,
                        'lang' => $locale,
                        'key' => $key
                    ],
                    [
                        'value' => $val
                    ]
                );
            }
        }
    }

    public function translations()
    {
        return $this->hasMany(Translation::class, 'translatable_id')->where('translatable_type', class_basename(__CLASS__));
    }
}
