rem
rem OpenMe for Collective Knowledge
rem
rem See LICENSE.txt for licensing details.
rem See Copyright.txt for copyright details.
rem
rem Developer: Grigori Fursin
rem

del org/openme_ck/openme_ck.class
del openme_ck.jar

rem Set your own path
set JAVA_HOME=C:\Program Files\Java\jdk1.6.0_41
set PATH=%JAVA_HOME%\bin;%PATH%

javac org/openme_ck/openme_ck.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
jar cf openme_ck.jar org\openme_ck\openme_ck.class
