<h2>Magento_GiftCardStaging module</h2>

## Overview

The Magento_GiftCardStaging module is a part of the staging functionality in Magento EE. It enables you to add GiftCard Product updates to the existing store campaigns. In other words, you can change the GiftCard Product attributes in campaigns. These updates are shown on the campaign dashboard.

## Implementation Details

The Magento_GiftCardStaging module changes the GiftCard Product creation page to make them compatible with the Magento Staging Framework:

- Adds the Amount field set to the Schedule Update form
- Provides functionality of the field set
- Returns Amounts values to the initial state after campaign is finished

## Dependencies

You can find the list of modules that have dependencies with the Magento_GiftCardStaging module in the `require` object of the `composer.json` file. The file is located in the same directory as this `README` file.

## Extension Points

Extension points enable extension developers to interact with the Magento_GiftCardStaging module. For more information about Magento extension mechanism, see [Magento plug-ins](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/plugins.html).

[Magento dependency injection mechanism](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/depend-inj.html) enables you to override the functionality of the Magento_GiftCardStaging module.

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.0/release-notes/changes/ee_changes.html).
