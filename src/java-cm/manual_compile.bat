rem
rem OpenME for Collective Mind
rem
rem See LICENSE.txt for licensing details.
rem See Copyright.txt for copyright details.
rem
rem Developer: Grigori Fursin
rem

del org/openme/openme.class
del openme.jar

javac org/openme/openme.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
jar cf openme.jar org\openme\openme.class
