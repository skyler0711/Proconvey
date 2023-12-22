<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OverviewPdfField extends Enum
{
    // Overview
    const Tenure = 'overview_tenure';

    const Price = 'overview_price';

    const PropertyType = 'overview_property_type';

    const PropertySubType = 'overview_property_sub_type';

    const CurrentUse = 'overview_current_use';

    const IntendedUse = 'overview_intended_use';

    const DependentOnSale = 'overview_dependent_on_sale';

    const DependentOnSaleAddress = 'overview_dependent_on_sale_address';

    const SharedOwnershipPercentage = 'overview_shared_ownership_percentage';

    const RelationshipToSeller = 'overview_relationship_to_seller';

    // Overview Details
    const SaleStatus = 'overview_sale_status';

    const DepositPaid = 'overview_deposit_paid';

    const DepositPaidAmount = 'overview_deposit_paid_amount';

    const PurchaseThroughEstateAgent = 'overview_purchase_through_estate_agent';

    const LegalRepresentationName = 'overview_legal_representation_name';

    const LegalRepresentationAddress = 'overview_legal_representation_address';

    const LegalRepresentationPhone = 'overview_legal_representation_phone';

    const LegalRepresentationEmail = 'overview_legal_representation_email';

    // The Buyers Overview Details
    const BuyerCapacity = 'overview_buyer_capacity';

    const TrustDeed = 'overview_trust_deed';

    const TrustDeedDetails = 'overview_trust_deed_details';

    // People
    const Type = 'overview_type';

    const NameChange = 'overview_name_change';

    const NameChangeReason = 'overview_name_change_reason';

    const NameChangeProof = 'overview_name_change_proof';

    const Representation = 'overview_representation';

    const Application = 'overview_application';

    const Authority = 'overview_authority';

    const Phone = 'overview_phone';

    const Email = 'overview_email';

    const Address = 'overview_address';

    const PostCompletionAddress = 'overview_post_completion_address';

    const SharedOwnershipPercentageValue = 'overview_shared_ownership_percentage_value';

    // People - Individual
    const Title = 'overview_title';

    const FirstName = 'overview_first_name';

    const MiddleName = 'overview_middle_name';

    const Surname = 'overview_last_name';

    const AltPhone = 'overview_alt_phone';

    const DateOfBirth = 'overview_date_of_birth';

    const Occupation = 'overview_occupation';

    const NationalInsurance = 'overview_national_insurance';

    // People - Company
    const VatRegistered = 'overview_vat_registered';

    const VatNumber = 'overview_vat_number';

    const CompanyNumber = 'overview_company_number';

    const CompanyName = 'overview_company_name';

    // The Sellers
    const SellerCompanyName = 'overview_seller_company_name';

    const SellerCompanyPhoneNumber = 'overview_seller_company_phone_number';

    // Bank Details
    const BuyerAccountName = 'overview_buyer_account_name';

    const BuyerAccountNumber = 'overview_buyer_account_number';

    const BuyerSortCode = 'overview_buyer_sort_code';

    // Stamp Duty Land Tax
    const IsThePropertyMoveable = 'overview_is_the_property_moveable';

    const MixtureResidentialAndNonResidential = 'overview_mixture_residential_and_non_residential';

    // Purchase Funds
    const PurchaseFundsOther = 'overview_purchase_funds_other';

    // Representatives
    const Representative = 'overview_representative';

    const RepresentativeRepresentation = 'overview_representative_representation';

    const RepresentativeAuthority = 'overview_representative_authority';

    const RepresentativeApplication = 'overview_representative_application';

    const RepresentativeUseSaleAddress = 'overview_representative_use_sale_address';

    // Representatives' representatives
    const RepresentativeRepresentatives = 'overview_representative_representatives';

    const RepresentativeRepresentativesUseSaleAddress = 'overview_representative_representatives_use_sale_address';

    const RepresentativeRepresentativesNameChange = 'overview_representative_representatives_name_change';

    const RepresentativeRepresentativesNameChangeReason = 'overview_representative_representatives_name_change_reason';

    const RepresentativeRepresentativesNameChangeProof = 'overview_representative_representatives_name_change_proof';

    // Mortgages, Loans, Transactions
    const SellerRelatedTransactions = 'overview_seller_related_transactions';

    const SellerRelatedTransactionAddresses = 'overview_seller_related_transactions_addresses';

    const MortgageLender = 'overview_mortgage_lender';

    const MortgageAmount = 'overview_mortgage_amount';

    // The Savings
    const SavingsAmount = 'overview_savings_amount';

    // Further Information
    const FurtherInformation = 'overview_further_information';
}
