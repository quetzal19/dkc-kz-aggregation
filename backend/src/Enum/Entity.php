<?php

namespace App\Enum;

enum Entity: string
{
    case SECTION = 'b_iblock_section';
    case ELEMENT = 'b_iblock_element';
    case ETIM_VALUE = 'etim_value';
    case ETIM_FEATURE = 'etim_feature';
    case ETIM_UNIT = 'etim_unit';
    case PRODUCT_FEATURE = 'product_features';
    case FACET_FEATURE_SECTION = 'facet_feature_section';
    case ETIM_ART_CLASS_FEATURE_MAP = 'etim_art_class_feature_map';
}
