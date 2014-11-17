rm org/openme/openme.class
rm openme.jar

rem Set your own path
export JAVA_HOME=/home/fursin/fggwork/java/jdk1.6.0_41
export PATH=$JAVA_HOME/bin:$PATH

javac org/openme/openme.java -classpath json-simple-1.1.1.jar;commons-codec-1.7.jar
jar cf openme.jar org/openme/openme.class
