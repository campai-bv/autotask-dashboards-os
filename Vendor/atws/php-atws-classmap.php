<?php namespace atws;
/*
 * Created on 27 Aug 2013
 * 
 */
class ClassMap {
	public function getClassMap() {
		return array(
			'GetAttachment' => 'GetAttachment',
			'GetAttachmentResponse' => 'GetAttachmentResponse',
			'Attachment' => 'Attachment',
			'AttachmentInfo' => 'AttachmentInfo',
			'CreateAttachment' => 'CreateAttachment',
			'CreateAttachmentResponse' => 'CreateAttachmentResponse',
			'DeleteAttachment' => 'DeleteAttachment',
			'DeleteAttachmentResponse' => 'DeleteAttachmentResponse',
			'QuoteLocation' => 'QuoteLocation',
			'Entity' => 'Entity',
			'Field' => 'Field',
			'PickListValue' => 'PickListValue',
			'UserDefinedField' => 'UserDefinedField',
			'AccountLocation' => 'AccountLocation',
			'Service' => 'Service',
			'ServiceBundle' => 'ServiceBundle',
			'ShippingType' => 'ShippingType',
			'Quote' => 'Quote',
			'QuoteItem' => 'QuoteItem',
			'PurchaseOrderItem' => 'PurchaseOrderItem',
			'PurchaseOrder' => 'PurchaseOrder',
			'PurchaseOrderReceive' => 'PurchaseOrderReceive',
			'InventoryTransfer' => 'InventoryTransfer',
			'InventoryItemSerialNumber' => 'InventoryItemSerialNumber',
			'InventoryItem' => 'InventoryItem',
			'InventoryLocation' => 'InventoryLocation',
			'Opportunity' => 'Opportunity',
			'ContractServiceBundleUnit' => 'ContractServiceBundleUnit',
			'ContractServiceUnit' => 'ContractServiceUnit',
			'ContractServiceAdjustment' => 'ContractServiceAdjustment',
			'ContractServiceBundleAdjustment' => 'ContractServiceBundleAdjustment',
			'ContractRetainer' => 'ContractRetainer',
			'ContractBlock' => 'ContractBlock',
			'ContractFactor' => 'ContractFactor',
			'ContractRate' => 'ContractRate',
			'ContractMilestone' => 'ContractMilestone',
			'ContractNote' => 'ContractNote',
			'ContractServiceBundle' => 'ContractServiceBundle',
			'ContractService' => 'ContractService',
			'TimeEntry' => 'TimeEntry',
			'Appointment' => 'Appointment',
			'ServiceCallTask' => 'ServiceCallTask',
			'ServiceCallTicket' => 'ServiceCallTicket',
			'ServiceCall' => 'ServiceCall',
			'ServiceCallTaskResource' => 'ServiceCallTaskResource',
			'ServiceCallTicketResource' => 'ServiceCallTicketResource',
			'Task' => 'Task',
			'Product' => 'Product',
			'ProductVendor' => 'ProductVendor',
			'Project' => 'Project',
			'Phase' => 'Phase',
			'Role' => 'Role',
			'Invoice' => 'Invoice',
			'AllocationCode' => 'AllocationCode',
			'Ticket' => 'Ticket',
			'Contact' => 'Contact',
			'TicketNote' => 'TicketNote',
			'AccountNote' => 'AccountNote',
			'Account' => 'Account',
			'Contract' => 'Contract',
			'InstalledProduct' => 'InstalledProduct',
			'Resource' => 'Resource',
			'BillingItem' => 'BillingItem',
			'ClientPortalUser' => 'ClientPortalUser',
			'ExpenseReport' => 'ExpenseReport',
			'ExpenseItem' => 'ExpenseItem',
			'GetEntityInfo' => 'GetEntityInfo',
			'EntityInfo' => 'EntityInfo',
			'GetEntityInfoResponse' => 'GetEntityInfoResponse',
			'GetFieldInfo' => 'GetFieldInfo',
			'GetFieldInfoResponse' => 'GetFieldInfoResponse',
			'getUDFInfo' => 'getUDFInfo',
			'getUDFInfoResponse' => 'getUDFInfoResponse',
			'query' => 'query',
			'ATWSResponse' => 'ATWSResponse',
			'ATWSError' => 'ATWSError',
			'EntityReturnInfo' => 'EntityReturnInfo',
			'EntityReturnInfoDatabaseAction' => 'EntityReturnInfoDatabaseAction',
			'EntityDuplicateStatus' => 'EntityDuplicateStatus',
			'queryResponse' => 'queryResponse',
			'AutotaskIntegrations' => 'AutotaskIntegrations',
			'create' => 'create',
			'createResponse' => 'createResponse',
			'update' => 'update',
			'updateResponse' => 'updateResponse',
			'delete' => 'delete',
			'deleteResponse' => 'deleteResponse',
			'getZoneInfo' => 'getZoneInfo',
			'ATWSZoneInfo' => 'ATWSZoneInfo',
			'getZoneInfoResponse' => 'getZoneInfoResponse',
			'getThresholdAndUsageInfo' => 'getThresholdAndUsageInfo',
			'getThresholdAndUsageInfoResponse' => 'getThresholdAndUsageInfoResponse',
		);
	}
}

class AccountLocation {
	public $LocationName; // anyType
	public $AccountID; // anyType
}

class AllocationCode {
	public $GeneralLedgerCode; // anyType
	public $Department; // anyType
	public $Name; // anyType
	public $Type; // anyType
	public $UseType; // anyType
	public $Description; // anyType
	public $Active; // anyType
	public $UnitCost; // anyType
	public $UnitPrice; // anyType
	public $AllocationCodeType; // anyType
	public $Taxable; // anyType
	public $ExternalNumber; // anyType
}

class Appointment {
	public $ResourceID; // anyType
	public $Title; // anyType
	public $StartDateTime; // anyType
	public $EndDateTime; // anyType
	public $Description; // anyType
	public $CreatorResourceID; // anyType
	public $CreateDateTime; // anyType
	public $UpdateDateTime; // anyType
}

class Attachment {
	public $Data; // base64Binary
	public $Info; // AttachmentInfo
}

class AttachmentInfo {
	public $ParentID; // anyType
	public $ParentType; // anyType
	public $Type; // anyType
	public $Title; // anyType
	public $FullPath; // anyType
	public $AttachDate; // anyType
	public $FileSize; // anyType
	public $AttachedByResourceID; // anyType
	public $AttachedByContactID; // anyType
	public $Publish; // anyType
}

class Contact {
	public $Active; // anyType
	public $AddressLine; // anyType
	public $AddressLine1; // anyType
	public $AlternatePhone; // anyType
	public $City; // anyType
	public $Country; // anyType
	public $CreateDate; // anyType
	public $EMailAddress; // anyType
	public $Extension; // anyType
	public $FaxNumber; // anyType
	public $FirstName; // anyType
	public $AccountID; // anyType
	public $LastName; // anyType
	public $LastActivityDate; // anyType
	public $LastModifiedDate; // anyType
	public $MiddleInitial; // anyType
	public $MobilePhone; // anyType
	public $Note; // anyType
	public $Notification; // anyType
	public $Phone; // anyType
	public $RoomNumber; // anyType
	public $State; // anyType
	public $Title; // anyType
	public $ZipCode; // anyType
}

class ContractServiceBundleUnit {
	public $ContractID; // anyType
	public $ServiceBundleID; // anyType
	public $StartDate; // anyType
	public $EndDate; // anyType
	public $Units; // anyType
	public $Price; // anyType
	public $Cost; // anyType
	public $ApproveAndPostDate; // anyType
}

class ContractServiceUnit {
	public $ContractID; // anyType
	public $ServiceID; // anyType
	public $StartDate; // anyType
	public $EndDate; // anyType
	public $Units; // anyType
	public $Price; // anyType
	public $Cost; // anyType
	public $VendorAccountID; // anyType
	public $ApproveAndPostDate; // anyType
}

class ContractServiceAdjustment {
	public $ContractID; // anyType
	public $ServiceID; // anyType
	public $EffectiveDate; // anyType
	public $UnitChange; // anyType
	public $AdjustedUnitPrice; // anyType
}

class ContractServiceBundleAdjustment {
	public $ContractID; // anyType
	public $ServiceBundleID; // anyType
	public $EffectiveDate; // anyType
	public $UnitChange; // anyType
	public $AdjustedUnitPrice; // anyType
}

class ContractRetainer {
	public $ContractID; // anyType
	public $Status; // anyType
	public $IsPaid; // anyType
	public $DatePurchased; // anyType
	public $DatePaid; // anyType
	public $StartDate; // anyType
	public $EndDate; // anyType
	public $Amount; // anyType
	public $InvoiceNumber; // anyType
	public $PaymentNumber; // anyType
	public $paymentID; // anyType
	public $OverrideHourlyRate; // anyType
	public $AmountApproved; // anyType
}

class ContractBlock {
	public $ContractID; // anyType
	public $IsPaid; // anyType
	public $DatePurchased; // anyType
	public $StartDate; // anyType
	public $EndDate; // anyType
	public $Hours; // anyType
	public $HourlyRate; // anyType
	public $InvoiceNumber; // anyType
	public $PaymentNumber; // anyType
	public $PaymentType; // anyType
	public $HoursApproved; // anyType
}

class ContractFactor {
	public $RoleID; // anyType
	public $ContractID; // anyType
	public $BlockHourFactor; // anyType
}

class ContractRate {
	public $RoleID; // anyType
	public $ContractID; // anyType
	public $ContractHourlyRate; // anyType
}

class ContractMilestone {
	public $CreateDate; // anyType
	public $CreatorResourceID; // anyType
	public $Status; // anyType
	public $DateDue; // anyType
	public $Amount; // anyType
	public $Title; // anyType
	public $Description; // anyType
	public $ContractID; // anyType
	public $IsInitialPayment; // anyType
	public $AllocationCodeID; // anyType
}

class ContractNote {
	public $ContractID; // anyType
	public $CreatorResourceID; // anyType
	public $LastActivityDate; // anyType
	public $Title; // anyType
	public $Description; // anyType
}

class ContractServiceBundle {
	public $ContractID; // anyType
	public $ServiceBundleID; // anyType
	public $UnitPrice; // anyType
	public $AdjustedPrice; // anyType
}

class ContractService {
	public $ContractID; // anyType
	public $ServiceID; // anyType
	public $UnitPrice; // anyType
	public $AdjustedPrice; // anyType
}

class CreateAttachment {
	public $attachment; // Attachment
}

class CreateAttachmentResponse {
	public $CreateAttachmentResult; // long
}

class DeleteAttachment {
	public $attachmentId; // long
}

class DeleteAttachmentResponse {
	public $DeleteAttachmentResult; // string
}

class Entity {
	public $Fields; // ArrayOfField
	public $id; // long
	public $UserDefinedFields; // ArrayOfUserDefinedField
}

class Field {
	public $Name; // string
	public $Label; // string
	public $Type; // string
	public $Length; // int
	public $Description; // string
	public $IsRequired; // boolean
	public $IsReadOnly; // boolean
	public $IsQueryable; // boolean
	public $IsReference; // boolean
	public $ReferenceEntityType; // string
	public $IsPickList; // boolean
	public $PicklistValues; // ArrayOfPickListValue
	public $PicklistParentValueField; // string
	public $DefaultValue; // string
}

class GetAttachment {
	public $attachmentId; // long
}

class GetAttachmentResponse {
	public $GetAttachmentResult; // Attachment
}

class InventoryTransfer {
	public $ProductID; // anyType
	public $FromLocationID; // anyType
	public $ToLocationID; // anyType
	public $QuantityTransferred; // anyType
	public $TransferByResourceID; // anyType
	public $TransferDate; // anyType
	public $Notes; // anyType
	public $SerialNumber; // anyType
}

class InventoryItemSerialNumber {
	public $InventoryItemID; // anyType
	public $SerialNumber; // anyType
}

class InventoryItem {
	public $ProductID; // anyType
	public $InventoryLocationID; // anyType
	public $QuantityOnHand; // anyType
	public $QuantityMinimum; // anyType
	public $QuantityMaximum; // anyType
	public $ReferenceNumber; // anyType
	public $Bin; // anyType
	public $OnOrder; // anyType
	public $BackOrder; // anyType
}

class InventoryLocation {
	public $LocationName; // anyType
	public $Active; // anyType
}

class Invoice {
	public $AccountID; // anyType
	public $CreatorResourceID; // anyType
	public $InvoiceDateTime; // anyType
	public $CreateDateTime; // anyType
	public $InvoiceNumber; // anyType
	public $Comments; // anyType
	public $InvoiceTotal; // anyType
	public $TaxGroup; // anyType
	public $FromDate; // anyType
	public $ToDate; // anyType
	public $OrderNumber; // anyType
	public $PaymentTerm; // anyType
	public $WebServiceDate; // anyType
	public $IsVoided; // anyType
	public $VoidedDate; // anyType
	public $VoidedByResourceID; // anyType
}

class Opportunity {
	public $AccountID; // anyType
	public $AdvancedField1; // anyType
	public $AdvancedField2; // anyType
	public $AdvancedField3; // anyType
	public $AdvancedField4; // anyType
	public $AdvancedField5; // anyType
	public $Amount; // anyType
	public $Barriers; // anyType
	public $ContactID; // anyType
	public $Cost; // anyType
	public $CreateDate; // anyType
	public $HelpNeeded; // anyType
	public $LeadReferral; // anyType
	public $Market; // anyType
	public $NextStep; // anyType
	public $OwnerResourceID; // anyType
	public $Probability; // anyType
	public $ProductID; // anyType
	public $ProjectedCloseDate; // anyType
	public $ProjectedLiveDate; // anyType
	public $PromotionName; // anyType
	public $RevenueSpread; // anyType
	public $RevenueSpreadUnit; // anyType
	public $Stage; // anyType
	public $Status; // anyType
	public $ThroughDate; // anyType
	public $Title; // anyType
	public $UseQuoteTotals; // anyType
	public $Rating; // anyType
	public $TotalAmountMonths; // anyType
}

class PickListValue {
	public $Value; // string
	public $Label; // string
	public $IsDefaultValue; // boolean
	public $SortOrder; // int
	public $parentValue; // string
}

class Phase {
	public $ParentPhaseID; // anyType
	public $ProjectID; // anyType
	public $Title; // anyType
	public $Description; // anyType
	public $StartDate; // anyType
	public $DueDate; // anyType
	public $Scheduled; // anyType
	public $CreateDate; // anyType
	public $CreatorResourceID; // anyType
	public $EstimatedHours; // anyType
	public $PhaseNumber; // anyType
	public $ExternalID; // anyType
	public $LastActivityDateTime; // anyType
}

class Product {
	public $Name; // anyType
	public $Description; // anyType
	public $SKU; // anyType
	public $Link; // anyType
	public $ProductCategory; // anyType
	public $ExternalProductID; // anyType
	public $UnitCost; // anyType
	public $UnitPrice; // anyType
	public $MSRP; // anyType
	public $DefaultVendorID; // anyType
	public $VendorProductNumber; // anyType
	public $ManufacturerName; // anyType
	public $ManufacturerProductName; // anyType
	public $Active; // anyType
	public $PeriodType; // anyType
	public $ProductAllocationCodeID; // anyType
	public $Serialized; // anyType
	public $CostAllocationCodeID; // anyType
}

class ProductVendor {
	public $ProductID; // anyType
	public $VendorID; // anyType
	public $VendorCost; // anyType
	public $VendorPartNumber; // anyType
	public $Active; // anyType
	public $IsDefault; // anyType
}

class Project {
	public $ProjectName; // anyType
	public $AccountID; // anyType
	public $Type; // anyType
	public $ExtProjectType; // anyType
	public $ExtPNumber; // anyType
	public $ProjectNumber; // anyType
	public $Description; // anyType
	public $CreateDateTime; // anyType
	public $CreatorResourceID; // anyType
	public $StartDateTime; // anyType
	public $EndDateTime; // anyType
	public $Duration; // anyType
	public $ActualHours; // anyType
	public $ActualBilledHours; // anyType
	public $EstimatedTime; // anyType
	public $LaborEstimatedRevenue; // anyType
	public $LaborEstimatedCosts; // anyType
	public $LaborEstimatedMarginPercentage; // anyType
	public $ProjectCostsRevenue; // anyType
	public $ProjectCostsBudget; // anyType
	public $ProjectCostEstimatedMarginPercentage; // anyType
	public $ChangeOrdersRevenue; // anyType
	public $ChangeOrdersBudget; // anyType
	public $SGDA; // anyType
	public $OriginalEstimatedRevenue; // anyType
	public $EstimatedSalesCost; // anyType
	public $Status; // anyType
	public $ContractID; // anyType
	public $ProjectLeadResourceID; // anyType
	public $CompanyOwnerResourceID; // anyType
	public $CompletedPercentage; // anyType
	public $CompletedDateTime; // anyType
	public $StatusDetail; // anyType
	public $StatusDateTime; // anyType
	public $Department; // anyType
	public $LineOfBusiness; // anyType
	public $PurchaseOrderNumber; // anyType
}

class PurchaseOrderItem {
	public $OrderID; // anyType
	public $ProductID; // anyType
	public $InventoryLocationID; // anyType
	public $Quantity; // anyType
	public $Memo; // anyType
	public $UnitCost; // anyType
}

class PurchaseOrder {
	public $VendorID; // anyType
	public $Status; // anyType
	public $CreatorResourceID; // anyType
	public $CreateDateTime; // anyType
	public $SubmitDateTime; // anyType
	public $CancelDateTime; // anyType
	public $ShipToName; // anyType
	public $ShipToAddress1; // anyType
	public $ShipToAddress2; // anyType
	public $ShipToCity; // anyType
	public $ShipToState; // anyType
	public $ShipToPostalCode; // anyType
	public $GeneralMemo; // anyType
	public $Phone; // anyType
	public $Fax; // anyType
	public $VendorInvoiceNumber; // anyType
	public $ExternalPONumber; // anyType
	public $PurchaseForAccountID; // anyType
	public $ShippingType; // anyType
	public $ShippingDate; // anyType
	public $Freight; // anyType
	public $TaxGroup; // anyType
	public $PaymentTerm; // anyType
}

class PurchaseOrderReceive {
	public $PurchaseOrderItemID; // anyType
	public $QuantityPreviouslyReceived; // anyType
	public $QuantityNowReceiving; // anyType
	public $ReceiveDate; // anyType
	public $QuantityBackOrdered; // anyType
	public $ReceivedByResourceID; // anyType
	public $SerialNumber; // anyType
}

class Quote {
	public $OpportunityID; // anyType
	public $Name; // anyType
	public $eQuoteActive; // anyType
	public $EffectiveDate; // anyType
	public $ExpirationDate; // anyType
	public $CreateDate; // anyType
	public $CreatorResourceID; // anyType
	public $ContactID; // anyType
	public $TaxGroup; // anyType
	public $ProposalProjectID; // anyType
	public $BillToLocationID; // anyType
	public $ShipToLocationID; // anyType
	public $SoldToLocationID; // anyType
	public $ShippingType; // anyType
	public $PaymentType; // anyType
	public $PaymentTerm; // anyType
	public $ExternalQuoteNumber; // anyType
	public $PurchaseOrderNumber; // anyType
	public $Comment; // anyType
	public $Description; // anyType
	public $AccountID; // anyType
	public $CalculateTaxSeparately; // anyType
	public $GroupByProductCategory; // anyType
	public $ShowEachTaxInGroup; // anyType
}

class QuoteItem {
	public $QuoteID; // anyType
	public $Type; // anyType
	public $ProductID; // anyType
	public $CostID; // anyType
	public $LaborID; // anyType
	public $ExpenseID; // anyType
	public $ShippingID; // anyType
	public $ServiceID; // anyType
	public $ServiceBundleID; // anyType
	public $Name; // anyType
	public $UnitPrice; // anyType
	public $UnitCost; // anyType
	public $Quantity; // anyType
	public $IsTaxable; // anyType
	public $IsOptional; // anyType
	public $PeriodType; // anyType
	public $Description; // anyType
	public $UnitDiscount; // anyType
	public $PercentageDiscount; // anyType
	public $LineDiscount; // anyType
}

class QuoteLocation {
	public $Address1; // anyType
	public $Address2; // anyType
	public $City; // anyType
	public $State; // anyType
	public $PostalCode; // anyType
}

class Service {
	public $Name; // anyType
	public $Description; // anyType
	public $UnitPrice; // anyType
	public $PeriodType; // anyType
	public $AllocationCodeID; // anyType
	public $IsActive; // anyType
	public $CreatorResourceID; // anyType
	public $UpdateResourceID; // anyType
	public $CreateDate; // anyType
	public $LastModifiedDate; // anyType
	public $VendorAccountID; // anyType
	public $UnitCost; // anyType
}

class ServiceBundle {
	public $Name; // anyType
	public $Description; // anyType
	public $old_selected_service_sum; // anyType
	public $UnitPrice; // anyType
	public $UnitDiscount; // anyType
	public $PercentageDiscount; // anyType
	public $PeriodType; // anyType
	public $AllocationCodeID; // anyType
	public $IsActive; // anyType
	public $CreatorResourceID; // anyType
	public $UpdateResourceID; // anyType
	public $CreateDate; // anyType
	public $LastModifiedDate; // anyType
	public $UnitCost; // anyType
}

class ServiceCall {
	public $AccountID; // anyType
	public $StartDateTime; // anyType
	public $EndDateTime; // anyType
	public $Description; // anyType
	public $Complete; // anyType
	public $CreatorResourceID; // anyType
	public $CreateDateTime; // anyType
	public $LastModifiedDateTime; // anyType
}

class ServiceCallTask {
	public $ServiceCallID; // anyType
	public $TaskID; // anyType
}

class ServiceCallTaskResource {
	public $ServiceCallTaskID; // anyType
	public $ResourceID; // anyType
}

class ServiceCallTicket {
	public $ServiceCallID; // anyType
	public $TicketID; // anyType
}

class ServiceCallTicketResource {
	public $ServiceCallTicketID; // anyType
	public $ResourceID; // anyType
}

class ShippingType {
	public $Name; // anyType
	public $IsActive; // anyType
	public $Description; // anyType
}

class Task {
	public $PhaseID; // anyType
	public $ProjectID; // anyType
	public $Title; // anyType
	public $Description; // anyType
	public $StartDateTime; // anyType
	public $EndDateTime; // anyType
	public $DepartmentID; // anyType
	public $AllocationCodeID; // anyType
	public $AssignedResourceID; // anyType
	public $AssignedResourceRoleID; // anyType
	public $TaskIsBillable; // anyType
	public $TaskType; // anyType
	public $Status; // anyType
	public $CompletedDateTime; // anyType
	public $CreateDateTime; // anyType
	public $CreatorResourceID; // anyType
	public $EstimatedHours; // anyType
	public $TaskNumber; // anyType
	public $ExternalID; // anyType
	public $LastActivityDateTime; // anyType
	public $Priority; // anyType
	public $PurchaseOrderNumber; // anyType
}

class TimeEntry extends Entity{
	public $TaskID; // anyType
	public $TicketID; // anyType
	public $InternalAllocationCodeID; // anyType
	public $Type; // anyType
	public $DateWorked; // anyType
	public $StartDateTime; // anyType
	public $EndDateTime; // anyType
	public $HoursWorked; // anyType
	public $HoursToBill; // anyType
	public $OffsetHours; // anyType
	public $SummaryNotes; // anyType
	public $InternalNotes; // anyType
	public $RoleID; // anyType
	public $CreateDateTime; // anyType
	public $ResourceID; // anyType
	public $CreatorUserID; // anyType
	public $LastModifiedUserID; // anyType
	public $LastModifiedDateTime; // anyType
	public $AllocationCodeID; // anyType
	public $ContractID; // anyType
	public $ShowOnInvoice; // anyType
	public $NonBillable; // anyType
}

class UserDefinedField {
	public $Name; // string
	public $Value; // string
}

class Role {
	public $Name; // anyType
	public $Description; // anyType
	public $HourlyFactor; // anyType
	public $HourlyRate; // anyType
}

class Ticket extends Entity{
	public $AccountID; // anyType
	public $AllocationCodeID; // anyType
	public $CompletedDate; // anyType
	public $ContactID; // anyType
	public $ContractID; // anyType
	public $CreateDate; // anyType
	public $CreatorResourceID; // anyType
	public $Description; // anyType
	public $DueDateTime; // anyType
	public $EstimatedHours; // anyType
	public $InstalledProductID; // anyType
	public $IssueType; // anyType
	public $LastActivityDate; // anyType
	public $Priority; // anyType
	public $QueueID; // anyType
	public $AssignedResourceID; // anyType
	public $AssignedResourceRoleID; // anyType
	public $Source; // anyType
	public $Status; // anyType
	public $SubIssueType; // anyType
	public $TicketNumber; // anyType
	public $Title; // anyType
	public $FirstResponseDateTime; // anyType
	public $ResolutionPlanDateTime; // anyType
	public $ResolvedDateTime; // anyType
	public $FirstResponseDueDateTime; // anyType
	public $ResolutionPlanDueDateTime; // anyType
	public $ResolvedDueDateTime; // anyType
	public $ServiceLevelAgreementID; // anyType
	public $ServiceLevelAgreementHasBeenMet; // anyType
	public $Resolution; // anyType
	public $PurchaseOrderNumber; // anyType
	public $TicketType; // it's an int id
}

class TicketNote {
	public $CreatorResourceID; // anyType
	public $Description; // anyType
	public $LastActivityDate; // anyType
	public $NoteType; // anyType
	public $Publish; // anyType
	public $TicketID; // anyType
	public $Title; // anyType
}

class AccountNote {
	public $AccountID; // anyType
	public $ContactID; // anyType
	public $OpportunityID; // anyType
	public $AssignedResourceID; // anyType
	public $ActionType; // anyType
	public $StartDateTime; // anyType
	public $EndDateTime; // anyType
	public $CompletedDateTime; // anyType
	public $Name; // anyType
	public $Note; // anyType
	public $LastModifiedDate; // anyType
}

class Account {
	public $Address1; // anyType
	public $Address2; // anyType
	public $AlternatePhone1; // anyType
	public $AlternatePhone2; // anyType
	public $AssetValue; // anyType
	public $City; // anyType
	public $CompetitorID; // anyType
	public $Country; // anyType
	public $CreateDate; // anyType
	public $Fax; // anyType
	public $KeyAccountIcon; // anyType
	public $LastActivityDate; // anyType
	public $MarketSegmentID; // anyType
	public $AccountName; // anyType
	public $AccountNumber; // anyType
	public $OwnerResourceID; // anyType
	public $ParentAccountID; // anyType
	public $Phone; // anyType
	public $PostalCode; // anyType
	public $SICCode; // anyType
	public $State; // anyType
	public $StockMarket; // anyType
	public $StockSymbol; // anyType
	public $TerritoryID; // anyType
	public $AccountType; // anyType
	public $WebAddress; // anyType
}

class Contract {
	public $AccountID; // anyType
	public $BillingPreference; // anyType
	public $Compliance; // anyType
	public $ContactName; // anyType
	public $ContractCategory; // anyType
	public $ContractName; // anyType
	public $ContractNumber; // anyType
	public $ContractPeriodType; // anyType
	public $ContractType; // anyType
	public $IsDefaultContract; // anyType
	public $Description; // anyType
	public $EndDate; // anyType
	public $EstimatedCost; // anyType
	public $EstimatedHours; // anyType
	public $EstimatedRevenue; // anyType
	public $OverageBillingRate; // anyType
	public $SetupFee; // anyType
	public $StartDate; // anyType
	public $Status; // anyType
	public $TimeReportingRequiresStartAndStopTimes; // anyType
	public $ServiceLevelAgreementID; // anyType
	public $PurchaseOrderNumber; // anyType
}

class InstalledProduct {
	public $CreateDate; // anyType
	public $AccountID; // anyType
	public $Active; // anyType
	public $DailyCost; // anyType
	public $HourlyCost; // anyType
	public $InstallDate; // anyType
	public $MonthlyCost; // anyType
	public $Notes; // anyType
	public $NumberOfUsers; // anyType
	public $PerUseCost; // anyType
	public $ProductID; // anyType
	public $ReferenceNumber; // anyType
	public $ReferenceTitle; // anyType
	public $SerialNumber; // anyType
	public $SetupFee; // anyType
	public $WarrantyExpirationDate; // anyType
	public $ContractID; // anyType
	public $ServiceID; // anyType
	public $ServiceBundleID; // anyType
	public $Type; // anyType
	public $Location; // anyType
	public $ContactID; // anyType
	public $VendorID; // anyType
	public $InstalledByID; // anyType
}

class Resource {
	public $Active; // anyType
	public $Email; // anyType
	public $Email2; // anyType
	public $Email3; // anyType
	public $EmailTypeCode; // anyType
	public $EmailTypeCode2; // anyType
	public $EmailTypeCode3; // anyType
	public $FirstName; // anyType
	public $Gender; // anyType
	public $Greeting; // anyType
	public $Initials; // anyType
	public $LastName; // anyType
	public $LocationID; // anyType
	public $MiddleName; // anyType
	public $MobilePhone; // anyType
	public $OfficeExtension; // anyType
	public $OfficePhone; // anyType
	public $ResourceType; // anyType
	public $Suffix; // anyType
	public $Title; // anyType
	public $UserName; // anyType
	public $UserType; // anyType
	public $DefaultServiceDeskRoleID; // anyType
}

class BillingItem {
	public $Type; // anyType
	public $SubType; // anyType
	public $ItemName; // anyType
	public $Description; // anyType
	public $Quantity; // anyType
	public $Rate; // anyType
	public $TotalAmount; // anyType
	public $OurCost; // anyType
	public $ItemDate; // anyType
	public $ApprovedTime; // anyType
	public $InvoiceID; // anyType
	public $ItemApproverID; // anyType
	public $AccountID; // anyType
	public $TicketID; // anyType
	public $TaskID; // anyType
	public $ProjectID; // anyType
	public $AllocationCodeID; // anyType
	public $RoleID; // anyType
	public $TimeEntryID; // anyType
	public $ContractID; // anyType
	public $WebServiceDate; // anyType
	public $NonBillable; // anyType
	public $TaxDollars; // anyType
	public $PurchaseOrderNumber; // anyType
}

class ClientPortalUser {
	public $SecurityLevel; // anyType
	public $ContactID; // anyType
	public $DateFormat; // anyType
	public $TimeFormat; // anyType
	public $NumberFormat; // anyType
	public $UserName; // anyType
	public $Password; // anyType
	public $ClientPortalActive; // anyType
}

class ExpenseReport {
	public $Name; // anyType
	public $Status; // anyType
	public $Submit; // anyType
	public $SubmitDate; // anyType
	public $SubmitterID; // anyType
	public $ApproverID; // anyType
	public $WeekEnding; // anyType
	public $ExpenseTotal; // anyType
	public $CashAdvanceAmount; // anyType
	public $RejectionReason; // anyType
	public $AmountDue; // anyType
	public $DepartmentNumber; // anyType
	public $QuickBooksReferenceNumber; // anyType
}

class ExpenseItem {
	public $ExpenseReportID; // anyType
	public $Description; // anyType
	public $ExpenseDate; // anyType
	public $ExpenseCategory; // anyType
	public $GLCode; // anyType
	public $WorkType; // anyType
	public $ExpenseAmount; // anyType
	public $PaymentType; // anyType
	public $Reimbursable; // anyType
	public $HaveReceipt; // anyType
	public $BillableToAccount; // anyType
	public $AccountID; // anyType
	public $ProjectID; // anyType
	public $TaskID; // anyType
	public $TicketID; // anyType
	public $EntertainmentLocation; // anyType
	public $Miles; // anyType
	public $Origin; // anyType
	public $Destination; // anyType
	public $Rejected; // anyType
	public $PurchaseOrderNumber; // anyType
}

class GetEntityInfo {
}

class EntityInfo {
	public $Name; // string
	public $CanUpdate; // boolean
	public $CanDelete; // boolean
	public $CanCreate; // boolean
	public $CanQuery; // boolean
	public $HasUserDefinedFields; // boolean
}

class GetEntityInfoResponse {
	public $GetEntityInfoResult; // ArrayOfEntityInfo
}

class GetFieldInfo {
	public $psObjectType; // string
}

class GetFieldInfoResponse {
	public $GetFieldInfoResult; // ArrayOfField
}

class getUDFInfo {
	public $psTable; // string
}

class getUDFInfoResponse {
	public $getUDFInfoResult; // ArrayOfField
}

class query {
	public $sXML; // string
	function __construct($sXMLin =""){
		$this->sXML= $sXMLin;
	}
}

class ATWSResponse {
	public $ReturnCode; // int
	public $EntityResults; // ArrayOfEntity
	public $EntityResultType; // string
	public $Errors; // ArrayOfATWSError
	public $EntityReturnInfoResults; // ArrayOfEntityReturnInfo
}

class ATWSError {
	public $Message; // string
}

class EntityReturnInfo {
	public $EntityId; // long
	public $DatabaseAction; // EntityReturnInfoDatabaseAction
	public $DuplicateStatus; // EntityDuplicateStatus
	public $Message; // string
}

class EntityReturnInfoDatabaseAction {
	const None = 'None';
	const Created = 'Created';
	const Updated = 'Updated';
}

class EntityDuplicateStatus {
	public $Found; // boolean
	public $MatchInfo; // string
	public $Ignored; // boolean
}

class queryResponse {
	public $queryResult; // ATWSResponse
}

class AutotaskIntegrations {
	public $PartnerID; // string
}

class create {
	public $Entities; // ArrayOfEntity
	public function __construct($entities){
		$this->Entities = $entities;
	
	}
}

class createResponse {
	public $createResult; // ATWSResponse
}

class update {
	public $Entities; // ArrayOfEntity
	public function __construct($inentities){
		$this->Entities = $inentities;
		
	}	
}

class updateResponse {
	public $updateResult; // ATWSResponse
}

class delete {
	public $Entities; // ArrayOfEntity
	public function __construct($entities){
		$this->Entities = $entities;
	
	}
}

class deleteResponse {
	public $deleteResult; // ATWSResponse
}

class getZoneInfo {
	public $UserName; // string
}

class ATWSZoneInfo {
	public $URL; // string
	public $ErrorCode; // int
	public $DataBaseType; // string
}

class getZoneInfoResponse {
	public $getZoneInfoResult; // ATWSZoneInfo
}

class getThresholdAndUsageInfo {
}

class getThresholdAndUsageInfoResponse {
	public $getThresholdAndUsageInfoResult; // ATWSResponse
}
 
?>
