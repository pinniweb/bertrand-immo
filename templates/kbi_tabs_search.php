<!--begin tabs-->
<?php $kbi_tabs_search = 1; ?>
<div id="tabs-search">
    <ul>
        <li id="kbi_tab_link_loc"><a href="#location">Location</a></li>
        <li id="kbi_tab_link_loc_saison"><a href="#vacation_rental">Location saisonnière</a></li>
        <li id="kbi_tab_link_vente"><a href="#sale">Vente</a></li>
    </ul>
    <div id="location">
       <?php include "kbi_search_location.php"; ?>
    </div>
    <div id="vacation_rental">
        <?php include "kbi_search_vacation_rental.php"; ?>
    </div>
    <div id="sale">
        <?php include "kbi_search_sale.php"; ?>
    </div>
</div>
<style>
    #tabs-search,#tabs-search > .ui-widget-header{
        background-color: transparent!important;
    }
    #tabs-search > .ui-widget-header{
        border: none;
        display: flex;
        justify-content: center;
        padding: 0;
    }
    #tabs-search > .ui-widget-header li{
        width: 100%;
        margin: 0;
        border: none;
        border-radius: 0;
        padding: 5px 0;
        font-size: 15px;
    }
    #tabs-search > .ui-widget-header li:hover a{
        color: white;
    }
    #tabs-search > .ui-widget-header li, #tabs-search > .ui-widget-header li a{
        width: 100%;
        text-align: center;
    }

    #kbi_tab_link_loc{
        border-top-left-radius: 3px;
    }
    #kbi_tab_link_vente{
        border-top-right-radius: 3px;
    }


    #tabs-search > .ui-widget-header li {
        margin: 0 5px 0 0;
        padding: 0;
    }
    #tabs-search > .ui-widget-header li:last-child {
        margin: 0;
    }


    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active {
        background: #0000007A!important;
    }
    .ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover {
        background: #ec5d60bf!important;
    }
    .ui-state-default a{
        color: #fff!important;
    }
    .ui-widget-content {
        background: #0000007A !important;
    }


    .adv_search_slider label, .adv_search_slider span{
        color: white!important;
    }
</style>