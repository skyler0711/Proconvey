<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class StepType extends Enum
{
    const DirectorDetails = 'director_details';

    const OwnerName = 'owner_name';

    const Owner = 'owner';

    const Address = 'address';

    const Tenure = 'tenure';

    const EstateAgent = 'estate_agent';

    const SoldStatus = 'sold_status';

    const Seller = 'seller';

    const Buyer = 'buyer';

    const BuyerExpanded = 'buyer_expanded';

    const SalePrice = 'sale_price';

    const BuyersSolicitor = 'buyers_solicitor';

    const MortgageLender = 'mortgage_lender';

    const MortgageAmount = 'mortgage_amount';

    const SDLT = 'sdlt';

    const MortgageBroker = 'mortgage_broker';

    // Company form representatives
    const CompanyFormPowerOfAttorneyRepresentative = 'company_form_power_of_attorney_representative';

    const CompanyFormDeputyshipOrderRepresentative = 'company_form_deputyship_order_representative';

    const CompanyFormGrantOfProbateRepresentative = 'company_form_grant_of_probate_representative';

    const SellersSolicitorSelectable = 'sellers_solicitor_selectable';

    const Charges = 'charges';

    const NameChange = 'name_change';

    const RepeatableNameChangeOwner = 'repeatable_name_change_owner';

    const RepeatableNameChangeAttorney = 'repeatable_name_change_attorney';

    const RepeatableNameChangeDeputy = 'repeatable_name_change_deputy';

    const RepeatableNameChangeExecutor = 'repeatable_name_change_executor';

    const DeputyDropdown = 'deputy_dropdown';

    const CompanyRepresentative = 'company_representative';

    const Custom = 'custom';

    const Loaner = 'loaner';

    const BuyerGiftor = 'buyer_giftor';

    const RemortgageGiftor = 'remortgage_giftor';

    const Attorney = 'attorney';

    const Deputy = 'deputy';

    const Mortgager = 'mortgager';

    const MortgageChargeLoan = 'mortgage_charge_loan';

    const OwnerFormPowerOfAttorney = 'owner_form_power_of_attorney';

    const MortgageRelatedTransactions = 'mortgage_related_transactions';

    const SavingsAmount = 'savings_amount';

    const TA9Secretary = 'ta9_secretary';

    const TA9ManagingAgent = 'ta9_managing_agent';
}

StepType::macro('getGenerationSteps', function () {
    return [
        StepType::SellersSolicitorSelectable,
        StepType::MortgageRelatedTransactions,
    ];
});

StepType::macro('getSinglePersonPdfFields', function () {
    return [
        StepType::TA9ManagingAgent,
        StepType::TA9Secretary,
    ];
});
