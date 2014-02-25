@ECHO OFF
SETLOCAL EnableDelayedExpansion

:: Vars
SET NEARD_NODEJS_PATH=%~dp0
SET NEARD_NODEJS_PATH=!NEARD_NODEJS_PATH:~0,-1!
SET NEARD_NODEJS_NPM_PATH=%NEARD_NODEJS_PATH%\node_modules\npm
SET NEARD_NODEJS_CONFIG_PATH=%NEARD_NODEJS_NPM_PATH%\npmrc

:: Relocate and edit NPM global config file
ECHO prefix = "%NEARD_NODEJS_PATH%"\>%NEARD_NODEJS_CONFIG_PATH%
ECHO globalconfig = "%NEARD_NODEJS_NPM_PATH%\npmrc">>%NEARD_NODEJS_CONFIG_PATH%
ECHO globalignorefile = "%NEARD_NODEJS_NPM_PATH%\npmignore">>%NEARD_NODEJS_CONFIG_PATH%
ECHO init-module = "%NEARD_NODEJS_NPM_PATH%\init.js">>%NEARD_NODEJS_CONFIG_PATH%
ECHO cache = "%NEARD_NODEJS_NPM_PATH%\cache">>%NEARD_NODEJS_CONFIG_PATH%

:: Create paths
IF NOT EXIST "%NEARD_NODEJS_NPM_PATH%\npmignore" ECHO. 2>"%NEARD_NODEJS_NPM_PATH%\npmignore"
IF NOT EXIST "%NEARD_NODEJS_NPM_PATH%\init.js" ECHO. 2>"%NEARD_NODEJS_NPM_PATH%\init.js"
IF NOT EXIST "%NEARD_NODEJS_NPM_PATH%\cache" MKDIR "%NEARD_NODEJS_NPM_PATH%\cache"

"%NEARD_NODEJS_PATH%\nodevars.bat" & "%NEARD_NODEJS_PATH%\npm" config set globalconfig "%NEARD_NODEJS_CONFIG_PATH%" --global

ENDLOCAL