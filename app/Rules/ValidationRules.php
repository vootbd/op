<?php

namespace App\Rules;

class ValidationRules
{
    /**
     * @action = 'n' or 'u' or 'd'
     */
    public static function island($action = 'n',$data=[])
    {
        $result = [
            'rules' => [],
            'message' => []
        ];
        switch ($action) {
            case 'n':
                $result = [
                    'rules' => [
                        'name' => 'required|max:40',
                        'code' => 'required|max:5|unique:islands,code',
                        'jurisdiction' => 'max:255',
                        'autonomous_code' => 'max:255'
                    ],
                    'messages' => [
                        'name.max' => trans('island.max_char'),
                        'code.unique' => trans('island.unique_code'),
                        'code.max' => trans('island.code_max'),
                        'jurisdiction.max' => trans('error.max_char'),
                        'autonomous_code.max' => trans('error.max_char')
                    ]
                ];
                break;
            case 'u':
                $result = [
                    'rules' => [
                        'id' => 'required',
                        'name' => 'required|max:40',
                        'code' => 'required|max:5|unique:islands,code,'.$data['id'],
                        'jurisdiction' => 'max:255',
                        'autonomous_code' => 'max:255'
                    ],
                    'messages' => [
                        'name.max' => trans('island.max_char'),
                        'code.unique' => trans('island.unique_code'),
                        'code.max' => trans('island.code_max'),
                        'jurisdiction.max' => trans('error.max_char'),
                        'autonomous_code.max' => trans('error.max_char')
                    ]
                ];
                break;
            case 'd':
                $result = [
                    'rules' => [
                        'id' => 'required'
                    ],
                    'messages' => [
                        'id.required' => trans('csv.id_error')
                    ]
                ];
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * @action = 'n' or 'u' or 'd'
     */
    public static function category($action = 'n')
    {
        $result = [
            'rules' => [],
            'message' => []
        ];
        switch ($action) {
            case 'n':
                $result = [
                    'rules' => [
                        'name' => 'required|max:40'
                    ],
                    'messages' => [
                        'name.max' => trans('category.max_char'),
                    ]
                ];
                break;
            case 'u':
                $result = [
                    'rules' => [
                        'id' => 'required',
                        'name' => 'required|max:40'
                    ],
                    'messages' => [
                        'name.max' => trans('category.max_char'),
                    ]
                ];
                break;
            case 'd':
                $result = [
                    'rules' => [
                        'id' => 'required'
                    ],
                    'messages' => [
                        'id.required' => trans('csv.id_error')
                    ]
                ];
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * @action = 'n' or 'u' or 'd'
     */
    public static function product($action = 'n')
    {
        $result = [
            'rules' => [],
            'message' => []
        ];
        switch ($action) {
            case 'n':
                $result = [
                    'rules' => [
                        'island_id' => 'required',
                        'seller_id' => 'required',
                        'status' => 'required|in:0,1',
                        'name' => 'required|max:40',
                        'product_explanation' => 'required|max:2000',
                        'category_id' => 'required',
                        'price' => 'required|max:7',
                        'tax' => 'required|in:8,10',
                        'shipment_method' => 'max:100',
                        'preservation_method' => 'max:100',
                        'package_type' => 'max:100',
                        'quality_retention_temperature' => 'max:100',
                        'expiration_taste_quality' => 'max:2000',
                        'use_scene' => 'max:2000',
                        'url' => 'max:255',
                        'allergy_display_obligation' => 'max:2000',
                        'allergy_display_recommended' => 'max:2000',
                        'assumed_customer_information' => 'max:100',
                        'number_of_inputs_per_case' => 'max:100',
                        'largest_smallest_case_delivery_unit_maximum' => 'max:100',
                        'largest_smallest_case_delivery_unit_minimum' => 'max:100',
                        'case_size_and_weight_verticle' => 'max:100',
                        'case_size_and_weight_horizontal' => 'max:100',
                        'case_size_and_weight_height' => 'max:100',
                        'case_size_and_weight_width' => 'max:100',
                        'order_lead_time' => 'max:100',
                        'available_time' => 'max:100',
                        'jan_code' => 'max:100',
                        'product_story_feelings_of_the_creator' => 'max:2000',
                        'product_features' => 'max:2000'
                    ],
                    'messages' => [
                        'island_id' => trans('product.island_select'),
                        'seller_id' => trans('product.seller_select'),
                        'status' => trans('product.stauts'),
                        'name' => trans('product.name'),
                        'name.max' => trans('product.name_max'),
                        'product_explanation' => trans('product.product_explanation'),
                        'product_explanation.max' => trans('product.max_char_2000'),
                        'category_id' => trans('product.category_select'),
                        'price' => trans('product.price'),
                        'price.max' => trans('product.price_max'),
                        'tax' => trans('product.tax'),
                        'shipment_method.max' => trans('product.shipment_method_max'),
                        'preservation_method.max' => trans('product.shipment_method_max'),
                        'package_type.max' => trans('product.shipment_method_max'),
                        'quality_retention_temperature.max' => trans('product.shipment_method_max'),
                        'expiration_taste_quality.max' => trans('product.max_char_2000'),
                        'use_scene.max' => trans('product.max_char_2000'),
                        'url.max' => trans('product.url_max'),
                        'allergy_display_obligation.max' => trans('product.max_char_2000'),
                        'allergy_display_recommended.max' => trans('product.max_char_2000'),
                        'assumed_customer_information.max' => trans('product.shipment_method_max'),
                        'number_of_inputs_per_case.max' => trans('product.shipment_method_max'),
                        'largest_smallest_case_delivery_unit_maximum.max' => trans('product.shipment_method_max'),
                        'largest_smallest_case_delivery_unit_minimum.max' => trans('product.shipment_method_max'),
                        'contents_unit_description.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_verticle.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_horizontal.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_height.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_width.max' => trans('product.shipment_method_max'),
                        'order_lead_time.max' => trans('product.shipment_method_max'),
                        'jan_code.max' => trans('product.shipment_method_max'),
                        'available_time.max' => trans('product.shipment_method_max'),
                        'product_story_feelings_of_the_creator.max' => trans('product.max_char_2000'),
                        'product_features.max' => trans('product.max_char_2000')
                    ]
                ];
                break;
            case 'u':
                $result = [
                    'rules' => [
                        'id' => 'required',
                        'island_id' => 'required',
                        'seller_id' => 'required',
                        'status' => 'required|in:0,1',
                        'name' => 'required|max:40',
                        'product_explanation' => 'required|max:2000',
                        'category_id' => 'required',
                        'price' => 'required|max:7',
                        'tax' => 'required|in:8,10',
                        'shipment_method' => 'max:100',
                        'preservation_method' => 'max:100',
                        'package_type' => 'max:100',
                        'quality_retention_temperature' => 'max:100',
                        'expiration_taste_quality' => 'max:2000',
                        'use_scene' => 'max:2000',
                        'url' => 'max:255',
                        'allergy_display_obligation' => 'max:2000',
                        'allergy_display_recommended' => 'max:2000',
                        'assumed_customer_information' => 'max:100',
                        'number_of_inputs_per_case' => 'max:100',
                        'largest_smallest_case_delivery_unit_maximum' => 'max:100',
                        'largest_smallest_case_delivery_unit_minimum' => 'max:100',
                        'case_size_and_weight_verticle' => 'max:100',
                        'case_size_and_weight_horizontal' => 'max:100',
                        'case_size_and_weight_height' => 'max:100',
                        'case_size_and_weight_width' => 'max:100',
                        'order_lead_time' => 'max:100',
                        'available_time' => 'max:100',
                        'jan_code' => 'max:100',
                        'product_story_feelings_of_the_creator' => 'max:2000',
                        'product_features' => 'max:2000'
                    ],
                    'messages' => [
                        'island_id' => trans('product.island_select'),
                        'seller_id' => trans('product.seller_select'),
                        'status' => trans('product.stauts'),
                        'name' => trans('product.name'),
                        'name.max' => trans('product.name_max'),
                        'product_explanation' => trans('product.product_explanation'),
                        'product_explanation.max' => trans('product.max_char_2000'),
                        'category_id' => trans('product.category_select'),
                        'price' => trans('product.price'),
                        'price.max' => trans('product.price_max'),
                        'tax' => trans('product.tax'),
                        'shipment_method.max' => trans('product.shipment_method_max'),
                        'preservation_method.max' => trans('product.shipment_method_max'),
                        'package_type.max' => trans('product.shipment_method_max'),
                        'quality_retention_temperature.max' => trans('product.shipment_method_max'),
                        'expiration_taste_quality.max' => trans('product.max_char_2000'),
                        'use_scene.max' => trans('product.max_char_2000'),
                        'url.max' => trans('product.url_max'),
                        'allergy_display_obligation.max' => trans('product.max_char_2000'),
                        'allergy_display_recommended.max' => trans('product.max_char_2000'),
                        'assumed_customer_information.max' => trans('product.shipment_method_max'),
                        'number_of_inputs_per_case.max' => trans('product.shipment_method_max'),
                        'largest_smallest_case_delivery_unit_maximum.max' => trans('product.shipment_method_max'),
                        'largest_smallest_case_delivery_unit_minimum.max' => trans('product.shipment_method_max'),
                        'contents_unit_description.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_verticle.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_horizontal.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_height.max' => trans('product.shipment_method_max'),
                        'case_size_and_weight_width.max' => trans('product.shipment_method_max'),
                        'order_lead_time.max' => trans('product.shipment_method_max'),
                        'jan_code.max' => trans('product.shipment_method_max'),
                        'available_time.max' => trans('product.shipment_method_max'),
                        'product_story_feelings_of_the_creator.max' => trans('product.max_char_2000'),
                        'product_features.max' => trans('product.max_char_2000')
                    ]
                ];
                break;
            case 'd':
                $result = [
                    'rules' => [
                        'id' => 'required'
                    ],
                    'messages' => [
                        'id.required' => trans('csv.id_error')
                    ]
                ];
                break;
            default:
                break;
        }

        return $result;
    }
}
