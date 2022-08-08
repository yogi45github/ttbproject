/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            "theme": 'Magento_Theme/js/theme',
            "selectize":    "js/selectize"
        }
    },
    paths: {
        "carouselInit":     'Magento_Theme/js/carouselInit',
        "blockCollapse":    'js/sidebarCollapse',
        "animateNumber":    'Magento_Theme/js/jquery.animateNumber.min',
        "owlcarousel":      'Magento_Theme/js/owl.carousel.min',
        "customSelect":     "Magento_Theme/js/select2",
        "mobileMenuClick":        "Magento_Theme/js/mobilemenuclick"
    },
    shim: {
        "owlcarousel":      ["jquery"],
        "animateNumber":    ["jquery"],
        "mobileMenuClick":        ["jquery", "jquery/ui"]
    },
    deps: [
        "jquery",
        "jquery/jquery.mobile.custom",
        "mage/common",
        "mage/dataPost",
        "mage/bootstrap"
    ]
};