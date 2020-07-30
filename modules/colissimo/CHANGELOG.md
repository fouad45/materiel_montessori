# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.1] - 2019-11-05
### Added
- Add CN23 exceptions with some EU countries

### Fixed
- Fix default country for sender address in multistore All Shops context
- Fix table names
- Use pickup points address instead of invoice address in Coliship export
- Fix JS bug in widget
- Fix paginations and filters in backend lists (1.6)
- Fix PHP compatibility

## [1.2.0] - 2019-10-09
### Added
- Add Brexit mode to anticipate the withdrawal of the UK from EU
- Add CSV shipping numbers import in "postage in BO" mode
- Allow merchants to send return labels by mail to customers
- Allow merchants to ship from overseas departments
- Allow merchants to configure a return address different from the sender address
- Allow configuration of HS code + origin country + short description in a product and category level
- Add product codes for shipments inside overseas departments
- Allow merchants to print PDF documents without downloading them
- Allow merchants to download and print PDF documents in a single file

### Changed
- Update Front-Tracking page with a new Timeline
- Add ID column in dashboard, postage and deposit slip forms
- Add EORI number in CN23
- Move pickup point selection button below delivery option in PS 1.6

### Fixed
- Fix CSV MIME types
- Fix PHP compatibility
- Fix files management configuration save process
- Fix total weight and product weights for CN23

## [1.1.2] - 2019-09-04
### Fixed
- Fix mobile input on OPC mode

## [1.1.1] - 2019-08-28
### Fixed
- Fix Colissimo label table creation for fresh install
- Fix widget and mobile input init process on 1.6

## [1.1.0] - 2019-08-07
### Added
- Allow merchants to tie an order placed with a carrier different than Colissimo to the module
- Allow merchants to change the Colissimo service (home deliveries, pickup point) when creating a label
- Allow merchants to delete labels
- Allow merchants to select/unselect all shipments in one click when generating deposit slips
- Allow merchants to change the state of an order after creating labels
- Allow merchants to change the state of an order after creating deposit slips
- Add two CSS and JS empty files to be overridden
- Display insurance information for each shipments

### Changed
- New mobile input for Front Widget with prefix and country
- Update order shipping number after deleting labels
- Restrict the pickup point country to the customer's country
- Check if SOAP extension is loaded and display a warning in Deposit Slip page if it's not

### Fixed
- Improve compatibility of modal
- Fix display rules of carriers for Monaco & Andorra
- Replaced array_column with array_map for PHP < 5.5
- Remove Material icons for PS 1.7 themes compatibility reasons
- Fix escape of Widget addresses in PS 1.7
- Add missing id_customer in pickup point address
- Remove Content-Length header for compatibility reasons
- Fix translations in Admin Controllers
- Fix help texts in Colissimo Dashboard
- Fix return labels generation with "Franc de Taxe et de Douane" option

## [1.0.6] - 2019-06-06
### Changed
- Remove Content-Length header for ZPL/DPL label download
- Fix display of modal in BO
- Add check if zip PHP extension is enabled
- Prevent customers to validate an order without choosing a pickup point and providing a valid mobile number in OPC mode

### Fixed
- Fix compatibility of modal
- Fix conflict with autocomplete JS plugin
- Add phone number in pickup point address
- Add missing countDown JS function in few PS 1.6 versions
- Fix display of widget for tablets
- Anonymize password field of Colissimo account in BO

## [1.0.5] - 2019-04-09
### Added
- Add new field for EORI number in BO configuration
- Allow merchant to use local files or remote files for Widget
- Add PCS service (EU pickup-points)
- Add "What's new?" modal with latest changes

### Changed
- Replace "other" field with Colissimo Pickup Point ID in address
- Rename carriers name (fresh install only)
- Improve logs in hook newOrder

### Fixed
- Fix product code sent when Pickup Point shipment (handle aliases)

## [1.0.4] - 2019-02-04
### Added
- Improve error management and add more logs in Coliship
- Add CPassID value in Coliship export
- Add logs in case of failed return label download (Front-Office)
- Override of module's front templates is now possible
- Handle gr weight unit abbreviation
- Allow return shipment from Outre-Mer
- Allow "without signature" service for Outre-Mer destination
- Add more country available for pudo service

### Changed
- Update Colissimo webservices version to 2.0
- Handle 9-digits reference in FO tracking URL
- Add mobile phone format example when validating pickup-point
- Convert weight unit to grams in Coliship export
- Convert EOL character to CRLF in Coliship export
- Handle INet encoding in Coliship export
- Replace hard-coded country ISO
- Check HS Code format in module configuration (Back-Office tab)
- Hide cancelled orders in the Colissimo Deposit Slip interface
- Hide and prevent tracking update of orders older than 90 days in Colissimo Dashboard

### Fixed
- Fix mobile validation in PrestaShop 1.6.1.x + One-Page-Checkout mode
- Fix escape for address and city fields inside Front Widget
- Fix valid label formats
- Fix hook registration for carrier process
- Fix phone numbers fields in Coliship export
- Fix error in order history in PrestaShop 1.6.1.x
- Fix UX in module configuration (Back-Office tab)
- Fix Euro conversion
- Fix PHP error on version 5.3 & 5.4
- Fix SQL query cast
- Fix JS variable name
- Fix widget endpoint URL
- Fix Colissimo EU Zone 1 & 3 country list
- Fix menu creation for PrestaShop 1.7.0.x & 1.7.1.x

## [1.0.3] - 2018-12-21
### Changed
- Change documents storage & download process

### Fixed
- Fix file extension of CN23 when downloading

## [1.0.2] - 2018-12-13
### Fixed
- Fix BO field validation function

## [1.0.1] - 2018-12-12
### Added
- Pickup point ID is now displayed on order page after pickup point address inside Colissimo section

### Changed
- Remove check WS credentials

### Fixed
- Fix Front Widget compatibility with OPC
- Fix choice of carrier when customer is not logged in
- Fix URL after validating pickup point in the Front Widget
- Fix minor security issues
- Fix missing translation
- Add fields format checks in module configuration
- Fix missing JS variable

## [1.0.0] - 2018-10-12
### Added
- First stable version
