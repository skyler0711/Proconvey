<?php

namespace Database\Seeders;

use Database\Seeders\Forms\Purchase\Buyer_Company;
use Database\Seeders\Forms\Purchase\Buyer_Individual;
use Database\Seeders\Forms\Purchase\Form_Giftor_Details;
use Database\Seeders\Forms\Purchase\GettingStarted_TheProperty as Buyer_GettingStarted_TheProperty;
use Database\Seeders\Forms\Remortgage\GettingStarted_Remortgaging;
use Database\Seeders\Forms\Remortgage\Remortgager_Company;
use Database\Seeders\Forms\Remortgage\Remortgager_Individual;
use Database\Seeders\Forms\Sale\Enquiry_Conservatory;
use Database\Seeders\Forms\Sale\Enquiry_Decking;
use Database\Seeders\Forms\Sale\Enquiry_DrivewayHardstand;
use Database\Seeders\Forms\Sale\Enquiry_Porch;
use Database\Seeders\Forms\Sale\Enquiry_SepticTank;
use Database\Seeders\Forms\Sale\Enquiry_SewageTreatmentPlant;
use Database\Seeders\Forms\Sale\Enquiry_SolarPanelsLeased;
use Database\Seeders\Forms\Sale\Enquiry_SolarPanelsOwned;
use Database\Seeders\Forms\Sale\Enquiry_TenantsSitting;
use Database\Seeders\Forms\Sale\Enquiry_TenantsVacant;
use Database\Seeders\Forms\Sale\GettingStarted_MortgagesChargesLoans;
use Database\Seeders\Forms\Sale\GettingStarted_TheProperty as Sale_GettingStarted_TheProperty;
use Database\Seeders\Forms\Sale\Owner_Company;
use Database\Seeders\Forms\Sale\Owner_Individual;
use Database\Seeders\Forms\Sale\ProtocolForm_TA10_FittingsAndContent;
use Database\Seeders\Forms\Sale\ProtocolForm_TA6_PropertyInformation;
use Database\Seeders\Forms\Sale\ProtocolForm_TA7_LeaseholdInformation;
use Database\Seeders\Forms\Sale\ProtocolForm_TA9_CommonholdInformation;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            $this->call([
                // Sale
                Sale_GettingStarted_TheProperty::class,
                Owner_Individual::class,
                Owner_Company::class,
                GettingStarted_MortgagesChargesLoans::class,
                ProtocolForm_TA6_PropertyInformation::class,
                ProtocolForm_TA10_FittingsAndContent::class,
                ProtocolForm_TA7_LeaseholdInformation::class,
                ProtocolForm_TA9_CommonholdInformation::class,
                Enquiry_Conservatory::class,
                Enquiry_Decking::class,
                Enquiry_Porch::class,
                Enquiry_DrivewayHardstand::class,
                Enquiry_SolarPanelsLeased::class,
                Enquiry_SolarPanelsOwned::class,
                Enquiry_SewageTreatmentPlant::class,
                Enquiry_SepticTank::class,
                Enquiry_TenantsSitting::class,
                Enquiry_TenantsVacant::class,
                // Purchase
                Buyer_GettingStarted_TheProperty::class,
                Buyer_Individual::class,
                Buyer_Company::class,
                Form_Giftor_Details::class,
                // Remortgage
                GettingStarted_Remortgaging::class,
                Remortgager_Individual::class,
                Remortgager_Company::class,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
    }
}
