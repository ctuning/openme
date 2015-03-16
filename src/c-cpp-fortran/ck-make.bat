@echo off

rem
rem Make script for CK libraries
rem (depends on configured/installed compilers via CK)
rem
rem See CK LICENSE.txt for licensing details.
rem See CK Copyright.txt for copyright details.
rem
rem Developer(s): Grigori Fursin, 2015
rem

set CK_SOURCE_FILES=cJSON.c openme.c
set CK_INCLUDE_FILE=openme.h

echo.
echo Cleaning directory ...
echo.

call %CK_ENV_SCRIPT_CK%\bin\clean_universal.bat

echo.
echo Building static library ...
echo.

set CK_TARGET_FILE=%LIB_NAME%%CK_LIB_EXT%

set CK_TARGET_FILE_S=%CK_TARGET_FILE%

call %CK_ENV_SCRIPT_CK%\bin\build_static_lib_c.bat

if %errorlevel% neq 0 (
 echo.
 echo Building failed!
 goto err
)

echo.
echo Building dynamic library ...
echo.

set CK_TARGET_FILE=%LIB_NAME%%CK_DLL_EXT%

set CK_TARGET_FILE_D=%CK_TARGET_FILE%

call %CK_ENV_SCRIPT_CK%\bin\build_dynamic_lib_c.bat

if %errorlevel% neq 0 (
 echo.
 echo Building failed!
 goto err
)

echo.
echo Installing ...
echo.

mkdir ..\lib
copy /B %CK_TARGET_FILE_S% ..\lib
copy /B %CK_TARGET_FILE_D% ..\lib

mkdir ..\include
copy /B CK_INCLUDE_FILE ..\include


exit /b 0

:err
set /p x=Press any key to continue!
exit /b 1
