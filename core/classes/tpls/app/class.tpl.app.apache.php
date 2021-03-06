<?php

class TplAppApache
{
    const MENU = 'apache';
    const MENU_VERSIONS = 'apacheVersions';
    const MENU_SERVICE = 'apacheService';
    const MENU_DEBUG = 'apacheDebug';
    const MENU_MODULES = 'apacheModules';
    const MENU_ALIAS = 'apacheAlias';
    const MENU_VHOSTS = 'apacheVhosts';
    
    const ACTION_SWITCH_VERSION = 'switchApacheVersion';
    const ACTION_CHANGE_PORT = 'changeApachePort';
    const ACTION_INSTALL_SERVICE = 'installApacheService';
    const ACTION_REMOVE_SERVICE = 'removeApacheService';
    const ACTION_SWITCH_MODULE = 'switchApacheModule';
    const ACTION_ADD_ALIAS = 'addAlias';
    const ACTION_EDIT_ALIAS = 'editAlias';
    const ACTION_ADD_VHOST = 'addVhost';
    const ACTION_EDIT_VHOST = 'editVhost';
    const ACTION_LAUNCH_STARTUP = 'launchStartupApache';
    
    public static function process()
    {
        global $neardLang;
        
        return TplApp::getMenu($neardLang->getValue(Lang::APACHE), self::MENU, get_called_class());
    }
    
    public static function getMenuApache()
    {
        global $neardBins, $neardLang;
        
        $tplVersions = TplApp::getMenu($neardLang->getValue(Lang::VERSIONS), self::MENU_VERSIONS, get_called_class());
        $tplService = TplApp::getMenu($neardLang->getValue(Lang::SERVICE), self::MENU_SERVICE, get_called_class());
        $tplDebug = TplApp::getMenu($neardLang->getValue(Lang::DEBUG), self::MENU_DEBUG, get_called_class());
        $tplModules = TplApp::getMenu($neardLang->getValue(Lang::MODULES), self::MENU_MODULES, get_called_class());
        $tplAlias = TplApp::getMenu($neardLang->getValue(Lang::ALIASES), self::MENU_ALIAS, get_called_class());
        $tplVhosts = TplApp::getMenu($neardLang->getValue(Lang::VIRTUAL_HOSTS), self::MENU_VHOSTS, get_called_class());
        
        return
        
            // Items
            $tplVersions[TplApp::SECTION_CALL] . PHP_EOL .
            $tplService[TplApp::SECTION_CALL] . PHP_EOL .
            $tplDebug[TplApp::SECTION_CALL] . PHP_EOL .
            $tplModules[TplApp::SECTION_CALL] . PHP_EOL .
            $tplAlias[TplApp::SECTION_CALL] . PHP_EOL .
            $tplVhosts[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemNotepad(basename($neardBins->getApache()->getConf()), $neardBins->getApache()->getConf()) . PHP_EOL .
            TplAestan::getItemNotepad($neardLang->getValue(Lang::MENU_ACCESS_LOGS), $neardBins->getApache()->getAccessLog()) . PHP_EOL .
            TplAestan::getItemNotepad($neardLang->getValue(Lang::MENU_REWRITE_LOGS), $neardBins->getApache()->getRewriteLog()) . PHP_EOL .
            TplAestan::getItemNotepad($neardLang->getValue(Lang::MENU_ERROR_LOGS), $neardBins->getApache()->getErrorLog()) . PHP_EOL . PHP_EOL .
            
            // Actions
            $tplVersions[TplApp::SECTION_CONTENT] . PHP_EOL .
            $tplService[TplApp::SECTION_CONTENT] . PHP_EOL .
            $tplDebug[TplApp::SECTION_CONTENT] . PHP_EOL .
            $tplModules[TplApp::SECTION_CONTENT] . PHP_EOL .
            $tplAlias[TplApp::SECTION_CONTENT] . PHP_EOL .
            $tplVhosts[TplApp::SECTION_CONTENT] . PHP_EOL;
    }
    
    public static function getMenuApacheVersions()
    {
        global $neardBins;
        $items = '';
        $actions = '';
        
        foreach ($neardBins->getApache()->getVersionList() as $version) {
            $glyph = '';
            $apachePhpModule = $neardBins->getPhp()->getApacheModule($version);
            if ($apachePhpModule === false) {
                $glyph = TplAestan::GLYPH_WARNING;
            } elseif ($version == $neardBins->getApache()->getVersion()) {
                $glyph = TplAestan::GLYPH_CHECK;
            }
            
            $tplSwitchApacheVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $glyph),
                false, get_called_class()
            );
            
            // Item
            $items .= $tplSwitchApacheVersion[TplApp::SECTION_CALL] . PHP_EOL;
        
            // Action
            $actions .= PHP_EOL . $tplSwitchApacheVersion[TplApp::SECTION_CONTENT];
        }
        
        return $items . $actions;
    }
    
    public static function getActionSwitchApacheVersion($version)
    {
        global $neardBins;
        return TplApp::getActionRun(Action::SWITCH_VERSION, array($neardBins->getApache()->getName(), $version)) . PHP_EOL .
            TplApp::getActionExec() . PHP_EOL;
    }
    
    public static function getMenuApacheService()
    {
        global $neardLang, $neardBins;
        
        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($neardLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );
        
        $isLaunchStartup = $neardBins->getApache()->getLaunchStartup() == BinApache::LAUNCH_STARTUP_ON;
        $tplLaunchStartup = TplApp::getActionMulti(
            self::ACTION_LAUNCH_STARTUP, array($isLaunchStartup ? BinApache::LAUNCH_STARTUP_OFF : BinApache::LAUNCH_STARTUP_ON),
            array($neardLang->getValue(Lang::MENU_LAUNCH_STARTUP_SERVICE), $isLaunchStartup ? TplAestan::GLYPH_CHECK : ''),
            false, get_called_class()
        );
        
        $result = TplAestan::getItemActionServiceStart($neardBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($neardBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($neardBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($neardBins->getApache()->getName(), $neardBins->getApache()->getPort()),
                array(sprintf($neardLang->getValue(Lang::MENU_CHECK_PORT), $neardBins->getApache()->getPort()), TplAestan::GLYPH_LIGHT)
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL .
            $tplLaunchStartup[TplApp::SECTION_CALL] . PHP_EOL;
        
        $isInstalled = $neardBins->getApache()->getService()->isInstalled();
        if (!$isInstalled) {
            $tplInstallService = TplApp::getActionMulti(
                self::ACTION_INSTALL_SERVICE, null,
                array($neardLang->getValue(Lang::MENU_INSTALL_SERVICE), TplAestan::GLYPH_SERVICE_INSTALL),
                $isInstalled, get_called_class()
            );
            
            $result .= $tplInstallService[TplApp::SECTION_CALL] . PHP_EOL . PHP_EOL .
                $tplInstallService[TplApp::SECTION_CONTENT] . PHP_EOL;
        } else {
            $tplRemoveService = TplApp::getActionMulti(
                self::ACTION_REMOVE_SERVICE, null,
                array($neardLang->getValue(Lang::MENU_REMOVE_SERVICE), TplAestan::GLYPH_SERVICE_REMOVE),
                !$isInstalled, get_called_class()
            );
            
            $result .= $tplRemoveService[TplApp::SECTION_CALL] . PHP_EOL . PHP_EOL .
                $tplRemoveService[TplApp::SECTION_CONTENT] . PHP_EOL;
        }
        
        $result .= $tplChangePort[TplApp::SECTION_CONTENT] . PHP_EOL .
            $tplLaunchStartup[TplApp::SECTION_CONTENT] . PHP_EOL;
        
        return $result;
    }
    
    public static function getActionChangeApachePort()
    {
        global $neardLang, $neardBins;
    
        return TplApp::getActionRun(Action::CHANGE_PORT, array($neardBins->getApache()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }
    
    public static function getActionInstallApacheService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinApache::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
    
    public static function getActionRemoveApacheService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinApache::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
    
    public static function getMenuApacheDebug()
    {
        global $neardLang;
        
        return TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_VERSION_NUMBER),
            array($neardLang->getValue(Lang::DEBUG_APACHE_VERSION_NUMBER), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_COMPILE_SETTINGS),
            array($neardLang->getValue(Lang::DEBUG_APACHE_COMPILE_SETTINGS), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_COMPILED_MODULES),
            array($neardLang->getValue(Lang::DEBUG_APACHE_COMPILED_MODULES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_CONFIG_DIRECTIVES),
            array($neardLang->getValue(Lang::DEBUG_APACHE_CONFIG_DIRECTIVES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_VHOSTS_SETTINGS),
            array($neardLang->getValue(Lang::DEBUG_APACHE_VHOSTS_SETTINGS), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_LOADED_MODULES),
            array($neardLang->getValue(Lang::DEBUG_APACHE_LOADED_MODULES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_SYNTAX_CHECK),
            array($neardLang->getValue(Lang::DEBUG_APACHE_SYNTAX_CHECK), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL;
    }
    
    public static function getMenuApacheModules()
    {
        global $neardBins;
        $items = '';
        $actions = '';
    
        foreach ($neardBins->getApache()->getModulesFromConf() as $module => $switch) {
            $tplSwitchApacheModule = TplApp::getActionMulti(
                self::ACTION_SWITCH_MODULE, array($module, $switch),
                array($module, ($switch == ActionSwitchApacheModule::SWITCH_ON ? TplAestan::GLYPH_CHECK : '')),
                false, get_called_class()
            );
            
            // Item
            $items .= $tplSwitchApacheModule[TplApp::SECTION_CALL] . PHP_EOL;
            
            // Action
            $actions .= PHP_EOL . $tplSwitchApacheModule[TplApp::SECTION_CONTENT];
        }
    
        return $items . $actions;
    }
    
    public static function getActionSwitchApacheModule($module, $switch)
    {
        global $neardBins;
    
        $switch = $switch == ActionSwitchApacheModule::SWITCH_OFF ? ActionSwitchApacheModule::SWITCH_ON : ActionSwitchApacheModule::SWITCH_OFF;
        return TplApp::getActionRun(Action::SWITCH_APACHE_MODULE, array($module, $switch)) . PHP_EOL .
            TplService::getActionRestart(BinApache::SERVICE_NAME) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
    
    public static function getMenuApacheAlias()
    {
        global $neardLang, $neardBins;
    
        $tplAddAlias = TplApp::getActionMulti(
            self::ACTION_ADD_ALIAS, null,
            array($neardLang->getValue(Lang::MENU_ADD_ALIAS), TplAestan::GLYPH_ADD),
            false, get_called_class()
        );
        
        // Items
        $items = $tplAddAlias[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL;
        
        // Actions
        $actions = PHP_EOL . $tplAddAlias[TplApp::SECTION_CONTENT];
    
        foreach ($neardBins->getApache()->getAlias() as $alias) {
            $tplEditAlias = TplApp::getActionMulti(
                self::ACTION_EDIT_ALIAS, array($alias),
                array(sprintf($neardLang->getValue(Lang::MENU_EDIT_ALIAS), $alias), TplAestan::GLYPH_FILE),
                false, get_called_class()
            );
            
            // Items
            $items .= $tplEditAlias[TplApp::SECTION_CALL] . PHP_EOL;
            
            // Actions
            $actions .= PHP_EOL . PHP_EOL . $tplEditAlias[TplApp::SECTION_CONTENT];
        }
    
        return $items . $actions;
    }
    
    public static function getActionAddAlias()
    {
        return TplApp::getActionRun(Action::ADD_ALIAS) . PHP_EOL .
            TplAppReload::getActionReload();
    }
    
    public static function getActionEditAlias($alias)
    {
        return TplApp::getActionRun(Action::EDIT_ALIAS, array($alias)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
    
    public static function getMenuApacheVhosts()
    {
        global $neardLang, $neardBins;
    
        $tplAddVhost = TplApp::getActionMulti(
            self::ACTION_ADD_VHOST, null,
            array($neardLang->getValue(Lang::MENU_ADD_VHOST), TplAestan::GLYPH_ADD),
            false, get_called_class()
        );
    
        // Items
        $items = $tplAddVhost[TplApp::SECTION_CALL] . PHP_EOL .
        TplAestan::getItemSeparator() . PHP_EOL;
    
        // Actions
        $actions = PHP_EOL . $tplAddVhost[TplApp::SECTION_CONTENT];
    
        foreach ($neardBins->getApache()->getVhosts() as $vhost) {
            $tplEditVhost = TplApp::getActionMulti(
                self::ACTION_EDIT_VHOST, array($vhost),
                array(sprintf($neardLang->getValue(Lang::MENU_EDIT_VHOST), $vhost), TplAestan::GLYPH_FILE),
                false, get_called_class()
            );
    
            // Items
            $items .= $tplEditVhost[TplApp::SECTION_CALL] . PHP_EOL;
    
            // Actions
            $actions .= PHP_EOL . PHP_EOL . $tplEditVhost[TplApp::SECTION_CONTENT];
        }
    
        return $items . $actions;
    }
    
    public static function getActionAddVhost()
    {
        return TplApp::getActionRun(Action::ADD_VHOST) . PHP_EOL .
        TplAppReload::getActionReload();
    }
    
    public static function getActionEditVhost($vhost)
    {
        return TplApp::getActionRun(Action::EDIT_VHOST, array($vhost)) . PHP_EOL .
        TplAppReload::getActionReload();
    }
    
    public static function getActionLaunchStartupApache($launchStartup)
    {
        global $neardBins;
    
        return TplApp::getActionRun(Action::LAUNCH_STARTUP_SERVICE, array($neardBins->getApache()->getName(), $launchStartup)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
