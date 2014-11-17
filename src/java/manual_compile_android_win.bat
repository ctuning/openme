del org/openme/openme.class
del openme.jar

rem Set your own path
set JAVA_HOME=C:\Program Files\Java\jdk1.6.0_41
set PATH=%JAVA_HOME%\bin;%PATH%

javac org/openme/openme.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
jar cf openme.jar org\openme\openme.class
