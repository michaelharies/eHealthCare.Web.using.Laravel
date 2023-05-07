<?php
/*
 * File name: Media.php
 * Last modified: 2022.04.08 at 07:35:14
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Models;

use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media as BaseMedia;


/**
 * @property mixed size
 */
class Media extends BaseMedia implements HasMedia
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    protected $appends = [
        'url',
        'thumb',
        'icon',
        'formated_size'
    ];

    protected $hidden = [
        "responsive_images",
        "order_column",
        "created_at",
        "updated_at",
    ];

    public function getUrlAttribute()
    {
        return $this->getFullUrl();
    }

    public function getThumbAttribute()
    {
        if ($this->hasGeneratedConversion('thumb')) {
            return $this->getFirstMediaUrl('thumb');
        } else {
            return $this->getFullUrl();
        }
    }

    /**
     * to generate media url in case of fallback will
     * return the file type icon
     * @param string $conversion
     * @return string url
     */
    public function getFirstMediaUrl($conversion = '')
    {
        $url = $this->getUrl();
        $array = explode('.', $url);
        $extension = strtolower(end($array));
        if (in_array($extension, config('medialibrary.extensions_has_thumb'))) {
            return asset($this->getUrl($conversion));
        } else {
            return asset(config('medialibrary.icons_folder') . '/' . $extension . '.png');
        }
    }

    public function getIconAttribute()
    {
        if ($this->hasGeneratedConversion('icon')) {
            return $this->getFirstMediaUrl('icon');
        } else {
            return $this->getFullUrl();
        }
    }

    public function getFormatedSizeAttribute()
    {
        return formatedSize($this->size);
    }

    public function toArray(): array
    {
        if (!$this->hasGeneratedConversion('icon')) {
            parent::makeHidden('icon');
        }
        if (!$this->hasGeneratedConversion('thumb')) {
            parent::makeHidden('thumb');
        }
        parent::makeHidden(['model_type', 'model_id', 'collection_name', 'file_name', 'mime_type', 'disk', 'size', 'manipulations', 'custom_properties']);
        return parent::toArray();
    }
}
