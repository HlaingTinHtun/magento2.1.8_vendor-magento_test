<h2>Magento_LayeredNavigationStaging module</h2>

## Overview

The Magento_LayeredNavigationStaging module is a part of the staging functionality in Magento EE.
It restricts functionality of the Magento_LayeredNavigationStaging module in the staging preview mode.

## Implementation Details

The Magento_LayeredNavigationStaging module disables the Magento_LayeredNavigation module functionality in the staging preview mode.

## Dependencies

You can find the list of modules that have dependencies with the Magento_LayeredNavigationStaging module in the `require` object of the `composer.json` file. The file is located in the same directory as this `README` file.

## Extension Points

Extension points enable extension developers to interact with the Magento_LayeredNavigationStaging module. [Magento dependency injection mechanism](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/depend-inj.html) enables you to override the functionality of the Magento_LayeredNavigationStaging module.

### Layouts

You can extend and override layouts in the `Magento/LayeredNavigationStaging/view/frontend/layout/` directory.
For more information about layouts, see the [Layout documentation](http://devdocs.magento.com/guides/v2.1/frontend-dev-guide/layouts/layout-overview.html).

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.0/release-notes/changes/ee_changes.html).
