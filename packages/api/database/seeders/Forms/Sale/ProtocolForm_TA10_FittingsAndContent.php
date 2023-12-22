<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class ProtocolForm_TA10_FittingsAndContent extends Seeder
{
    const defaultFormItems = [
        ['name' => 'Included', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'included'],
        ['name' => 'Excluded', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'excluded'],
        ['name' => 'None', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'none'],
        ['name' => 'Price', 'type' => AnswerType::Text, 'placeholder' => 'e.g. £3500', 'pdfFieldSuffix' => 'price'],
        ['name' => 'Comments', 'type' => AnswerType::Text, 'placeholder' => 'Enter your comment', 'pdfFieldSuffix' => 'comments'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Form
        $form = Form::factory()
            ->state([
                'name' => 'TA10: Fittings and content',
                'group' => FormGroup::Protocol,
                'description' => 'Items included in the purchase of the property',
                'ta_form_template' => FormType::TA10FittingsAndContents,
                'order_number' => 8,
                'signature_coords' => [0.125, 0.715],
                'current_date_field' => ['date1', 'date2'],
                'type' => PropertyType::Sale,
            ])
            ->create();

        $answerId = Answer::whereHas('step', function ($query) {
            $query->where('question', 'Is the property for sale a freehold or leasehold?');
        })->first()->id;

        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Freehold',
            'type' => ConditionType::OR,
        ]);
        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Leasehold',
            'type' => ConditionType::OR,
        ]);
        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Commonhold',
            'type' => ConditionType::OR,
        ]);
        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Shared ownership',
            'type' => ConditionType::OR,
        ]);

        $this->basicFittings($form);
        $this->kitchen($form);
        $this->bathroom($form);
        $this->carpets($form);
        $this->curtainsAndCurtainRails($form);
        $this->lightFittings($form);
        $this->fittedUnits($form);
        $this->outdoorArea($form);
        $this->televisionAndTelephone($form);
        $this->stockAndFuel($form);
        $this->otherItems($form);
    }

    protected function basicFittings(Form $form)
    {
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Basic fittings',
                ])
                ->make()
                ->toArray()
        );

        // Basic Fittings
        $basicFittings = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerBasicFittings = $basicFittings->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Boiler / Immersion heater', 'pdfFieldPrefix' => 'boiler_immersion_heater'],
                            ['name' => 'Radiators / Wall heaters', 'pdfFieldPrefix' => 'radiator_wall_heaters'],
                            ['name' => 'Night-storage heaters', 'pdfFieldPrefix' => 'night_storage_heaters'],
                            ['name' => 'Free-standing heaters', 'pdfFieldPrefix' => 'free_standing_heater'],
                            ['name' => 'Gas fires (with surround)', 'pdfFieldPrefix' => 'gas_fires'],
                            ['name' => 'Electric fires (with surround)', 'pdfFieldPrefix' => 'electric_fires'],
                            ['name' => 'Light switches', 'pdfFieldPrefix' => 'light_switches'],
                            ['name' => 'Roof Insulation', 'pdfFieldPrefix' => 'roof_insulation'],
                            ['name' => 'Window fittings', 'pdfFieldPrefix' => 'window_fittings'],
                            ['name' => 'Window shutters / Grilles', 'pdfFieldPrefix' => 'window_shutters_grilles'],
                            ['name' => 'Internal door fittings', 'pdfFieldPrefix' => 'internal_door_fittings'],
                            ['name' => 'External door fittings', 'pdfFieldPrefix' => 'external_door_fittings'],
                            ['name' => 'Doorbell / Chime', 'pdfFieldPrefix' => 'doorbell_chime'],
                            ['name' => 'Electric sockets', 'pdfFieldPrefix' => 'electric_sockets'],
                            ['name' => 'Burglar alarm', 'pdfFieldPrefix' => 'burglar_alarm'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other items',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBasicFittings->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of basic fittings
    }

    protected function kitchen(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Kitchen',
                ])
                ->make()
                ->toArray()
        );

        // Kitchen
        $kitchen = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.#Please also indicate whether an item is fitted or freestanding.',
                ])
                ->make()
                ->toArray()
        );

        $answerKitchen = $kitchen->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Hob', 'pdfFieldPrefix' => 'hob'],
                            ['name' => 'Extractor hood', 'pdfFieldPrefix' => 'extractor_hood'],
                            ['name' => 'Oven / Grill', 'pdfFieldPrefix' => 'oven_grill'],
                            ['name' => 'Cooker', 'pdfFieldPrefix' => 'cooker'],
                            ['name' => 'Microwave', 'pdfFieldPrefix' => 'microwave'],
                            ['name' => 'Refrigerator / Fridge-freezer', 'pdfFieldPrefix' => 'refrigerator_fridge_freezer'],
                            ['name' => 'Freezer', 'pdfFieldPrefix' => 'freezer'],
                            ['name' => 'Dishwasher', 'pdfFieldPrefix' => 'dishwasher'],
                            ['name' => 'Tumble-dryer', 'pdfFieldPrefix' => 'tumble_dryer'],
                            ['name' => 'Washing machine', 'pdfFieldPrefix' => 'washing_machine'],

                        ],
                        'columns' => [
                            ['name' => 'Included', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'included'],
                            ['name' => 'Excluded', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'excluded'],
                            ['name' => 'None', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'none'],
                            ['name' => 'Fitted', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'fitted'],
                            ['name' => 'Freestanding', 'type' => AnswerType::Checkbox, 'pdfFieldSuffix' => 'freestanding'],
                            ['name' => 'Price', 'type' => AnswerType::Text, 'placeholder' => 'e.g. £3500', 'pdfFieldSuffix' => 'price'],
                            ['name' => 'Comments', 'type' => AnswerType::Text, 'placeholder' => 'Enter your comment', 'pdfFieldSuffix' => 'comments'],
                        ],
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other items',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerKitchen->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of kitchen
    }

    protected function bathroom(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Bathroom',
                ])
                ->make()
                ->toArray()
        );

        // Bathroom
        $bathroom = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerBathroom = $bathroom->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Bath', 'pdfFieldPrefix' => 'bath'],
                            ['name' => 'Shower fitting for bath', 'pdfFieldPrefix' => 'shower_fitting_for_bath'],
                            ['name' => 'Shower Curtain', 'pdfFieldPrefix' => 'shower_curtain'],
                            ['name' => 'Bathroom cabinet', 'pdfFieldPrefix' => 'bathroom_cabinet'],
                            ['name' => 'Taps', 'pdfFieldPrefix' => 'taps'],
                            ['name' => 'Separate shower and fittings', 'pdfFieldPrefix' => 'separate_shower_and_fittings'],
                            ['name' => 'Towel rail', 'pdfFieldPrefix' => 'towel_rail'],
                            ['name' => 'Soap / Toothbrush holders', 'pdfFieldPrefix' => 'soap_toothbrush_holders'],
                            ['name' => 'Toilet roll holders', 'pdfFieldPrefix' => 'toilet_roll_holders'],
                            ['name' => 'Bathroom mirror', 'pdfFieldPrefix' => 'bathroom_mirror'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other items',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBathroom->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of bathroom
    }

    protected function carpets(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Carpets',
                ])
                ->make()
                ->toArray()
        );

        // Carpets
        $carpets = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerCarpets = $carpets->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Hall, stairs and landing', 'pdfFieldPrefix' => 'carpets_hall_stairs_and_landing'],
                            ['name' => 'Living room', 'pdfFieldPrefix' => 'carpets_living_room'],
                            ['name' => 'Dining room', 'pdfFieldPrefix' => 'carpets_dining_room'],
                            ['name' => 'Kitchen', 'pdfFieldPrefix' => 'carpets_kitchen'],
                            ['name' => 'Bedroom 1', 'pdfFieldPrefix' => 'carpets_bedroom_1'],
                            ['name' => 'Bedroom 2', 'pdfFieldPrefix' => 'carpets_bedroom_2'],
                            ['name' => 'Bedroom 3', 'pdfFieldPrefix' => 'carpets_bedroom_3'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other rooms',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCarpets->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // End of carpets
    }

    protected function curtainsAndCurtainRails(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Curtains and curtain rails',
                ])
                ->make()
                ->toArray()
        );

        // Curtains and curtain rails
        $curtainsAndCurtainRails = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerCurtainsAndCurtainRails = $curtainsAndCurtainRails->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Hall, stairs and landing', 'pdfFieldPrefix' => 'curtain_rails_poles_pelmets_hall_stairs_and_landing'],
                            ['name' => 'Living room', 'pdfFieldPrefix' => 'curtain_rails_poles_pelmets_living_room'],
                            ['name' => 'Dining room', 'pdfFieldPrefix' => 'curtain_rails_poles_pelmets_dining_room'],
                            ['name' => 'Kitchen', 'pdfFieldPrefix' => 'curtain_rails_poles_pelmets_kitchen'],
                            ['name' => 'Bedroom 1', 'pdfFieldPrefix' => 'curtain_rails_poles_pelmets_bedroom_1'],
                            ['name' => 'Bedroom 2', 'pdfFieldPrefix' => 'curtain_rails_poles_pelmets_bedroom_2'],
                            ['name' => 'Bedroom 3', 'pdfFieldPrefix' => 'curtain_rails_poles_pelmets_bedroom_3'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other rooms',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCurtainsAndCurtainRails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of curtains and curtain rails

        // Curtains and blinds
        $curtainsAndBlinds = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerCurtainsAndBlinds = $curtainsAndBlinds->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Hall, stairs and landing', 'pdfFieldPrefix' => 'curtains_blinds_hall_stairs_and_landing'],
                            ['name' => 'Living room', 'pdfFieldPrefix' => 'curtains_blinds_living_room'],
                            ['name' => 'Dining room', 'pdfFieldPrefix' => 'curtains_blinds_dining_room'],
                            ['name' => 'Kitchen', 'pdfFieldPrefix' => 'curtains_blinds_kitchen'],
                            ['name' => 'Bedroom 1', 'pdfFieldPrefix' => 'curtains_blinds_bedroom_1'],
                            ['name' => 'Bedroom 2', 'pdfFieldPrefix' => 'curtains_blinds_bedroom_2'],
                            ['name' => 'Bedroom 3', 'pdfFieldPrefix' => 'curtains_blinds_bedroom_3'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other rooms',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCurtainsAndBlinds->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of curtains and blinds
    }

    protected function lightFittings(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Light fittings',
                ])
                ->make()
                ->toArray()
        );

        // Light fittings
        $lightFittings = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'If you remove a light fitting, it is assumed that you will replace the fitting with a ceiling rose, a flex, bulb holder and bulb and that they will be left in a safe condition.',
                    'sub_heading' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.#Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerLightFittings = $lightFittings->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Hall, stairs and landing', 'pdfFieldPrefix' => 'light_fittings_hall_stairs_and_landing'],
                            ['name' => 'Living room', 'pdfFieldPrefix' => 'light_fittings_living_room'],
                            ['name' => 'Dining room', 'pdfFieldPrefix' => 'light_fittings_dining_room'],
                            ['name' => 'Kitchen', 'pdfFieldPrefix' => 'light_fittings_kitchen'],
                            ['name' => 'Bedroom 1', 'pdfFieldPrefix' => 'light_fittings_bedroom_1'],
                            ['name' => 'Bedroom 2', 'pdfFieldPrefix' => 'light_fittings_bedroom_2'],
                            ['name' => 'Bedroom 3', 'pdfFieldPrefix' => 'light_fittings_bedroom_3'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other rooms',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLightFittings->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of light fittings
    }

    protected function fittedUnits(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Fitted units',
                ])
                ->make()
                ->toArray()
        );

        // Fitted units
        $fittedUnits = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Fitted units include, for example, fitted cupboards, fitted shelves, and fitted wardrobes.',
                    'sub_heading' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.#Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerFittedUnits = $fittedUnits->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Hall, stairs and landing', 'pdfFieldPrefix' => 'fitted_units_hall_stairs_and_landing'],
                            ['name' => 'Living room', 'pdfFieldPrefix' => 'fitted_units_living_room'],
                            ['name' => 'Dining room', 'pdfFieldPrefix' => 'fitted_units_dining_room'],
                            ['name' => 'Kitchen', 'pdfFieldPrefix' => 'fitted_units_kitchen'],
                            ['name' => 'Bedroom 1', 'pdfFieldPrefix' => 'fitted_units_bedroom_1'],
                            ['name' => 'Bedroom 2', 'pdfFieldPrefix' => 'fitted_units_bedroom_2'],
                            ['name' => 'Bedroom 3', 'pdfFieldPrefix' => 'fitted_units_bedroom_3'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other rooms',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFittedUnits->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Fitted Units
    }

    protected function outdoorArea(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Outdoor area',
                ])
                ->make()
                ->toArray()
        );

        // Outdoor area
        $outdoorArea = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerOutdoorArea = $outdoorArea->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Garden furniture', 'pdfFieldPrefix' => 'outdoor_area_garden_furniture'],
                            ['name' => 'Garden ornaments', 'pdfFieldPrefix' => 'outdoor_area_garden_ornaments'],
                            ['name' => 'Tree, plants, shrubs', 'pdfFieldPrefix' => 'outdoor_area_trees_plants_shrubs'],
                            ['name' => 'Barbecue', 'pdfFieldPrefix' => 'outdoor_area_barbecue'],
                            ['name' => 'Dustbins', 'pdfFieldPrefix' => 'outdoor_area_dustbins'],
                            ['name' => 'Garden shed', 'pdfFieldPrefix' => 'outdoor_area_garden_shed'],
                            ['name' => 'Greenhouse', 'pdfFieldPrefix' => 'outdoor_area_greenhouse'],
                            ['name' => 'Outdoor heater', 'pdfFieldPrefix' => 'outdoor_area_outdoor_heater'],
                            ['name' => 'Outside lights', 'pdfFieldPrefix' => 'outdoor_area_outside_lights'],
                            ['name' => 'Water butt', 'pdfFieldPrefix' => 'outdoor_area_water_butt'],
                            ['name' => 'Clothes line', 'pdfFieldPrefix' => 'outdoor_area_clothes_line'],
                            ['name' => 'Rotary line', 'pdfFieldPrefix' => 'outdoor_area_rotary_line'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                        'addMoreLabel' => 'Add other items',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOutdoorArea->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Outdoor area
    }

    protected function televisionAndTelephone(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Television and telephone',
                ])
                ->make()
                ->toArray()
        );

        // Television and telephone
        $televisionAndTelephone = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerTelevisionAndTelephone = $televisionAndTelephone->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Telephone receivers', 'pdfFieldPrefix' => 'television_and_telephone_telephone_receivers'],
                            ['name' => 'Television aerial', 'pdfFieldPrefix' => 'television_and_telephone_television_aerial'],
                            ['name' => 'Radio aerial', 'pdfFieldPrefix' => 'television_and_telephone_radio_aerial'],
                            ['name' => 'Satellite dish', 'pdfFieldPrefix' => 'television_and_telephone_satellite_dish'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => false,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTelevisionAndTelephone->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of television and telephone
    }

    protected function stockAndFuel(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Stock and fuel',
                ])
                ->make()
                ->toArray()
        );

        // Stock and fuel
        $stockAndFuel = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select whether an item is included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $answerStockAndFuel = $stockAndFuel->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [
                            ['name' => 'Oil', 'pdfFieldPrefix' => 'stock_of_fuel_oil'],
                            ['name' => 'Wood', 'pdfFieldPrefix' => 'stock_of_fuel_wood'],
                            ['name' => 'Liquefied petroleum gas (LPG)', 'pdfFieldPrefix' => 'stock_of_fuel_liquefied_petroleum_gas'],
                        ],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => false,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerStockAndFuel->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Stock and fuel
    }

    protected function otherItems(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Other items',
                ])
                ->make()
                ->toArray()
        );

        // Other items
        $otherItems = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please add any other items included in the sale, excluded from the sale or there is no such item at the property.',
                    'sub_heading' => 'Where an item is excluded from the sale you can insert a price to sell the item to the buyer if applicable. The buyer can then decide whether to accept your offer.',
                ])
                ->make()
                ->toArray()
        );

        $otherItems->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::DataTable,
                    'details' => [
                        'rows' => [],
                        'columns' => self::defaultFormItems,
                        'allowsAddMore' => true,
                    ],
                ])
                ->make()
                ->toArray()
        );
    }
}