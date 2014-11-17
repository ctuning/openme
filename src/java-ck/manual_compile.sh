#
# OpenME for Collective Knowledge
#
# See LICENSE.txt for licensing details.
# See Copyright.txt for copyright details.
#
# Developer: Grigori Fursin
#

rm org/openme_ck/openme_ck.class
rm openme_ck.jar

javac org/openme_ck/openme_ck.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
jar cf openme_ck.jar org/openme_ck/openme_ck.class
