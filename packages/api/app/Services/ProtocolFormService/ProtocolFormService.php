<?php

namespace App\Services\ProtocolFormService;

use App\Enums\AnswerType;
use App\Enums\FileTextAnswerTypes;
use App\Enums\FormType;
use App\Enums\StepType;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Property;
use App\Services\PdfService\PdfResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use mikehaertl\pdftk\Pdf;

class ProtocolFormService
{
    public static function getPdf(Form $form, Property $property): PdfResult
    {
        // Get the users that belong to the property
        $users = $property->users()->get();

        // Get the name of the Users with their title and first name and last name
        $userNames = [];
        foreach ($users as $user) {
            $userNames[] = $user->title . ' ' . $user->first_name . ' ' . $user->last_name;
        }

        // Get the Conveyancer
        $conveyancer = $property->conveyancer;

        // Get the Conveyancer Address
        $conveyancerAddress = $conveyancer->address;

        // Get the Conveyancer Address
        $conveyancerAddress = collect([
            $conveyancerAddress->line_1,
            $conveyancerAddress->line_2,
            $conveyancerAddress->city,
            $conveyancerAddress->postcode,
        ])->filter()->implode(', ');

        // Get the Property Address
        $propertyAddress = collect([
            $property->address->line_1,
            $property->address->line_2,
            $property->address->city,
        ])->filter()->implode(', ');

        // Get the Postcode and Split it by the letter and number
        $postcodeLetter = str_split($property->address->postcode);

        // Get the Form
        $form = $form->load('sections.steps.answers');

        $date = Carbon::now();

        // Get the PDF
        switch ($form->ta_form_template) {
            case FormType::TA6PropertyInformation:
                $pdf = new Pdf(resource_path('pdfs/ta6_property_information_form.pdf'));
                $newPDF = new Pdf(resource_path('pdfs/ta6_property_information_form.pdf'));
                break;
            case FormType::TA7LeaseholdInformation:
                $pdf = new Pdf(resource_path('pdfs/ta7_leasehold_information_form.pdf'));
                $newPDF = new Pdf(resource_path('pdfs/ta7_leasehold_information_form.pdf'));
                break;
            case FormType::TA9CommonholdInformation:
                $pdf = new Pdf(resource_path('pdfs/ta9_commonhold_information_form.pdf'));
                $newPDF = new Pdf(resource_path('pdfs/ta9_commonhold_information_form.pdf'));
                break;
            case FormType::TA10FittingsAndContents:
                $pdf = new Pdf(resource_path('pdfs/ta10_fittings_and_contents_form.pdf'));
                $newPDF = new Pdf(resource_path('pdfs/ta10_fittings_and_contents_form.pdf'));
                break;
            default:
                break;
        }

        // Get the field names
        $fieldNames = $pdf->getDataFields()->__toArray();

        // Get all the Provided Answers
        $allProvidedAnswers = $property->providedAnswers()->get();

        $fieldsToFill = [];

        $providedAnswers = [];

        foreach ($allProvidedAnswers as $providedAnswer) {
            if (!array_key_exists($providedAnswer->answer_id, $providedAnswers)) {
                $providedAnswers[$providedAnswer->answer_id] = [];
            }

            $providedAnswers[$providedAnswer->answer_id][] = $providedAnswer;

            // Get JSON from Details column
            $answerDetails = json_decode(collect([$providedAnswer->answer->details])->toJson());

            // Check the answer if it is a dropdown or text
            if (isset($answerDetails[0]->options)) {
                $answerDetails = $answerDetails[0]->options;

                collect($answerDetails)->filter(function ($a) use ($answerDetails, $providedAnswer, $fieldNames, &$fieldsToFill, $propertyAddress, $postcodeLetter, $property, $conveyancer, $userNames, $conveyancerAddress, $users, $date, $form) {
                    $answerExists = is_array($providedAnswer->value)
                        ? in_array($a->value, $providedAnswer->value)
                        : $providedAnswer->value === $a->value;

                    if ($answerExists) {
                        $field = collect($fieldNames)->filter(function ($field) use ($a) {
                            if ($field['FieldName'] === $a->pdfFormFieldName) {
                                return $field['FieldName'];
                            }
                        })->first();

                        if ($field !== null) {
                            $value = $a->value;

                            if ($providedAnswer->answer->type === AnswerType::Checkbox) {
                                if (isset($answerDetails[0]->altValue)) {
                                    if ($value === '1') {
                                        $value = $answerDetails[0]->altValue;
                                    } else {
                                        return;
                                    }
                                }
                            }

                            if (isset($a->altText)) {
                                $value = sprintf($a->altText, $value);
                            }

                            if (!isset($fieldsToFill[$field['FieldName']])) {
                                $fieldsToFill[$field['FieldName']] = $value;
                            } else {
                                $fieldsToFill[$field['FieldName']] .= "\n" . $value;
                            }
                        }

                        if ($field !== null) {
                            // TA6
                            if ($form->ta_form_template === FormType::TA6PropertyInformation) {
                                $fieldsToFill['address_of_property'] = $propertyAddress;
                                $fieldsToFill['postcode_1'] = $postcodeLetter[0];
                                $fieldsToFill['postcode_2'] = $postcodeLetter[1];
                                $fieldsToFill['postcode_3'] = $postcodeLetter[2];
                                $fieldsToFill['postcode_4'] = $postcodeLetter[3];
                                $fieldsToFill['postcode_5'] = $postcodeLetter[4];
                                $fieldsToFill['postcode_6'] = $postcodeLetter[5];
                                $fieldsToFill['postcode_7'] = array_key_exists(6, $postcodeLetter) ? $postcodeLetter[6] : '';
                                $fieldsToFill['postcode_8'] = array_key_exists(7, $postcodeLetter) ? $postcodeLetter[7] : '';
                                $fieldsToFill['reference_number'] = $property->case_reference;
                                $fieldsToFill['name_of_solicitor'] = $conveyancer->name;
                                $fieldsToFill['full_names_of_seller'] = implode(', ', $userNames);
                                $fieldsToFill['address_of_solicitor'] = $conveyancerAddress;
                            }

                            // TA7
                            if ($form->ta_form_template === FormType::TA7LeaseholdInformation) {
                                $fieldsToFill['address_line_1'] = $property->address->line_1;
                                $fieldsToFill['address_line_2'] = $property->address->line_2;
                                $fieldsToFill['address_town'] = $property->address->city;
                                $fieldsToFill['address_postcode'] = $property->address->postcode;
                                $fieldsToFill['name_of_solicitors_firm'] = $conveyancer->name;
                                $fieldsToFill['solicitor_address_line_1'] = $conveyancer->address->line_1;
                                $fieldsToFill['solicitor_address_line_2'] = $conveyancer->address->line_2;
                                $fieldsToFill['solicitor_address_town'] = $conveyancer->address->city;
                                $fieldsToFill['solicitor_address_postcode'] = $conveyancer->address->postcode;
                                $fieldsToFill['solicitor_reference_number'] = $property->case_reference;

                                for ($i = 0; $i < count($users); $i++) {
                                    $fieldsToFill["first_name_" . ($i + 1)] = isset($users[$i]) ? $users[$i]->first_name : null;
                                    $fieldsToFill["last_name_" . ($i + 1)] = isset($users[$i]) ? $users[$i]->last_name : null;
                                }
                            }

                            // TA9
                            if ($form->ta_form_template === FormType::TA9CommonholdInformation) {
                                $fieldsToFill['1_date'] = $date->isoFormat('DD')[0];
                                $fieldsToFill['2_date'] = $date->isoFormat('DD')[1];
                                $fieldsToFill['3_date'] = $date->isoFormat('MM')[0];
                                $fieldsToFill['4_date'] = $date->isoFormat('MM')[1];
                                $fieldsToFill['5_date'] = $date->isoFormat('YY')[0];
                                $fieldsToFill['6_date'] = $date->isoFormat('YY')[1];
                            }
                        }
                    }
                });
            } else {
                if ($providedAnswer->answer->type === AnswerType::DataTable) {
                    $checkboxAnswers = json_decode(collect([optional($providedAnswer->answer->details)->rows])->toJson());

                    if (!isset($checkboxAnswers)) {
                        continue;
                    }

                    if ($providedAnswer->value !== null) {
                        Log::info('$providedAnswer->value:', ['$providedAnswer->value' => $providedAnswer->value]);
                        foreach ($providedAnswer->value['columns'] as $rowIndex => $value) {
                            foreach ($value as $columnIndex => $value) {
                                if (!is_numeric($columnIndex)) {
                                    continue;
                                }

                                $prefix = $providedAnswer->answer->details->rows[$rowIndex]->pdfFieldPrefix ?? '';
                                Log::info('$providedAnswer->value:', ['$providedAnswer->value' => $providedAnswer->value]);
                                $suffix = $providedAnswer->answer->details->columns[$columnIndex]->pdfFieldSuffix ?? '';
                                Log::info('$providedAnswer->value:', ['$providedAnswer->value' => $providedAnswer->value]);

                                if ($value !== null && isset($providedAnswer->answer->details->rows[$columnIndex]->type) && $providedAnswer->answer->details->rows[$columnIndex]->type === 'checkbox') {
                                    $value = $value === '1' ? 'Yes' : 'No';
                                }

                                if ($prefix !== '' && $suffix !== '') {
                                    $fieldsToFill[$prefix . '_' . $suffix] = $value;

                                    continue;
                                }
                             }
                        }
                    }

                    $fieldsToFill['address_of_property'] = $propertyAddress;
                    $fieldsToFill['address_of_property'] = $propertyAddress;
                    $fieldsToFill['postcode_1'] = $postcodeLetter[0];
                    $fieldsToFill['postcode_2'] = $postcodeLetter[1];
                    $fieldsToFill['postcode_3'] = $postcodeLetter[2];
                    $fieldsToFill['postcode_4'] = $postcodeLetter[3];
                    $fieldsToFill['postcode_5'] = $postcodeLetter[4];
                    $fieldsToFill['postcode_6'] = array_key_exists(5, $postcodeLetter) ? $postcodeLetter[5] : '';
                    $fieldsToFill['postcode_7'] = array_key_exists(6, $postcodeLetter) ? $postcodeLetter[6] : '';
                    $fieldsToFill['postcode_8'] = array_key_exists(7, $postcodeLetter) ? $postcodeLetter[7] : '';
                    $fieldsToFill['solicitor_reference_number'] = $property->case_reference;
                    $fieldsToFill['name_of_solicitor'] = $conveyancer->name;
                    $fieldsToFill['full_names_of_seller'] = implode(', ', $userNames);
                    $fieldsToFill['address_of_solicitor'] = $conveyancerAddress;
                } elseif ($providedAnswer->answer->type === AnswerType::File) {
                    $answerDetails = $providedAnswer->answer->details;

                    // Handle text boxes when no prefix given
                    if ((isset($answerDetails->pdfFormField) && isset($answerDetails->textAnswers)) && !isset($answerDetails->pdfFieldPrefix)) {
                        // Set to empty string if not defined
                        if (!isset($fieldsToFill[$answerDetails->pdfFormField])) {
                            $fieldsToFill[$answerDetails->pdfFormField] = '';
                        }

                        if (isset($providedAnswer->value['url']) || $providedAnswer->value === 1) {
                            $fieldsToFill[$answerDetails->pdfFormField] .= "\n" . $answerDetails->textAnswers[FileTextAnswerTypes::Enclosed];
                        }

                        if ($providedAnswer->value === 'Add later') {
                            $fieldsToFill[$answerDetails->pdfFormField] .= "\n" . $answerDetails->textAnswers[FileTextAnswerTypes::AddLater];
                        }

                        if ($providedAnswer->value === 'Not applicable') {
                            $fieldsToFill[$answerDetails->pdfFormField] .= "\n" . $answerDetails->textAnswers[FileTextAnswerTypes::NotApplicable];
                        }
                    }

                    // Handle checkboxes when no text details given
                    if ((!isset($answerDetails->pdfFormField) && !isset($answerDetails->textAnswers)) && isset($answerDetails->pdfFieldPrefix)) {
                        // Get checkboxes for file...
                        $checkboxFields = collect($fieldNames)
                            ->filter(function ($field) use ($providedAnswer) {
                                if (strpos($field['FieldName'], $providedAnswer->answer->details->pdfFieldPrefix . '_') === 0) {
                                    return true;
                                }
                            })
                            ->mapWithKeys(function ($field) use ($providedAnswer) {
                                // Give array friendly keys
                                $fieldName = substr($field['FieldName'], strlen($providedAnswer->answer->details->pdfFieldPrefix . '_'));

                                return [$fieldName => $field];
                            })
                            ->toArray();

                        // If the client selects 'Not applicable', do nothing
                        if ($providedAnswer->value === 'Not applicable') {
                            continue;
                        }

                        // If there is an image url, set enclosed
                        if (isset($providedAnswer->value['url'])) {
                            // TA7 uses 'Attached' instead of 'Enclosed'
                            if ($form->ta_form_template === FormType::TA7LeaseholdInformation) {
                                $fieldsToFill[$checkboxFields['attached']['FieldName']] = $checkboxFields['attached']['FieldStateOption'][0];
                            } else {
                                $fieldsToFill[$checkboxFields['enclosed']['FieldName']] = $checkboxFields['enclosed']['FieldStateOption'][0];
                            }
                        }

                        // If the client selects 'Add later', set to follow
                        if ($providedAnswer->value === 'Add later') {
                            $fieldsToFill[$checkboxFields['to_follow']['FieldName']] = $checkboxFields['to_follow']['FieldStateOption'][0];
                        }
                    }
                } else {
                    if (isset($answerDetails[0])) {
                        $field = collect($fieldNames)->filter(function ($field) use ($answerDetails) {
                            if ($field['FieldName'] === $answerDetails[0]->pdfFormFieldName) {
                                return $field['FieldName'];
                            }
                        })->first();

                        if ($field !== null) {
                            $value = $providedAnswer->value;

                            if ($providedAnswer->answer->type === AnswerType::Checkbox) {
                                if (isset($answerDetails[0]->altValue)) {
                                    if ($value === '1') {
                                        $value = $answerDetails[0]->altValue;
                                    } else {
                                        continue;
                                    }
                                }
                            }

                            /**
                             * If the Answer value is an array it might be a repeatable step
                             * If the Answer is part of a repeatable step
                             * For that step add the "complied answers to the pdf field. But only once
                             */
                            if (is_array($value)) {
                                $repeatableAnswerId = $providedAnswer->answer->step->repeatable_answer_id;
                                if (isset($repeatableAnswerId)) {
                                    $repeatQuestion = Answer::query()->find($repeatableAnswerId); // Question which determines how many times to repeat
                                    $repeatableProvidedAnswer = //collect($providedAnswers)->where('answer_id', $repeatableAnswerId)->first(); // Provided answer for the repeatable question
                                        $repeatQuestion
                                        ->providedAnswers()
                                        ->where('property_id', $property->id)
                                        ->first();

                                    if (is_numeric($repeatableProvidedAnswer->value)) {
                                        $fieldsToFill[$field['FieldName']] = implode(
                                            "\n",
                                            $providedAnswer->answer->step->getCompiledAnswer($property)
                                        );

                                        continue;
                                    } else {
                                        continue;
                                    }
                                }
                            }

                            // Replace with alt text if exists
                            if (isset($answerDetails[0]->altText)) {
                                $value = sprintf($answerDetails[0]->altText, $value);
                            }

                            if (!isset($fieldsToFill[$field['FieldName']])) {
                                $fieldsToFill[$field['FieldName']] = $value;
                            } else {
                                // If the line requires special generation use the step to gain the special value
                                if (in_array($providedAnswer->answer->step->type, StepType::getSinglePersonPdfFields())) {
                                    $fieldsToFill[$field['FieldName']] = implode(
                                        "\n",
                                        $providedAnswer->answer->step->getCompiledAnswer($property)
                                    );
                                } else {
                                    $fieldsToFill[$field['FieldName']] .= "\n" . $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Get the list of possible current date fields
        if ($form->current_date_field) {
            foreach ($form->current_date_field as $field) {
                $fieldsToFill[$field] = now()->format('Y-m-d');
            }
        }

        // Fill the PDF
        $result = $newPDF
            ->fillForm($fieldsToFill)
            ->needAppearances()
            ->flatten();

        // Check if the PDF was filled
        if ($result === false) {
            $error = $result->getError();
            throw new \Exception($error);
        }

        // Get the number of pages
        $numberOfPages = (new Pdf($result))->getData()['NumberOfPages'];

        // Return the PDF
        return new PdfResult($result->toString(), $numberOfPages);
    }
}
