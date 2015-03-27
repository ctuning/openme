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

del org\openme_ck\openme_ck.class
del openme_ck.jar

echo.
echo Building ...
echo.

javac org/openme_ck/openme_ck.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
if %errorlevel% neq 0 (
 echo.
 echo Building failed!
 goto err
)

jar cf openme_ck.jar org\openme_ck\openme_ck.class
if %errorlevel% neq 0 (
 echo.
 echo Building failed!
 goto err
)

echo.
echo Installing ...
echo.

mkdir %INSTALL_DIR%\lib
copy /B *.jar %INSTALL_DIR%\lib

exit /b 0

:err
set /p x=Press any key to continue!
exit /b 1
