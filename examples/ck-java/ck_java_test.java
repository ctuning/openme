//Import libraries...
import org.openme_ck.openme_ck;

import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.JSONValue;

public class ck_java_test
{
    public static void main(String[] args)
    {
        JSONObject i=null;
        JSONObject r=null;

        System.out.println("Test of Collective Knowledge light java binding:");
        System.out.println("");

        /************************************************************/
        System.out.println("************************************************************");
        System.out.println("List all modules ...");
        System.out.println("");

        i=new JSONObject();
        i.put("action","list");
        i.put("module_uoa","module");

        r=openme_ck.access(i);

        if ((Long)r.get("return")>0)
        {
           System.out.println("CK error: "+r.get("error")+"!");
           System.exit(1);
        }

        JSONArray lst=(JSONArray) r.get("lst");

        for (int l=0; l<lst.size(); l++)
        {
           JSONObject obj = (JSONObject) lst.get(l);
           String data_uoa = (String) obj.get("data_uoa");
           System.out.println(" * "+data_uoa);
        }

        /************************************************************/
        System.out.println("************************************************************");
        System.out.println("Find path to module 'kernel' ...");
        System.out.println("");

        i=new JSONObject();
        i.put("action","find");
        i.put("module_uoa","module");
        i.put("data_uoa","kernel");

        r=openme_ck.access(i);

        if ((Long)r.get("return")>0)
        {
           System.out.println("CK error: "+r.get("error")+"!");
           System.exit(1);
        }

        String path=(String) r.get("path");
        System.out.println("Path: "+path);

        /************************************************************/
        System.out.println("************************************************************");
        System.out.println("Trying to cause error with wrong action ...");
        System.out.println("");

        i=new JSONObject();
        i.put("action","listx");
        i.put("module_uoa","module");

        r=openme_ck.access(i);

        if ((Long)r.get("return")>0)
        {
           System.out.println("CK error: "+r.get("error")+"!");
        }

        /************************************************************/
        System.out.println("************************************************************");
        System.out.println("Generating CK UID ...");
        System.out.println("");

        i=new JSONObject();
        i.put("action","uid");

        r=openme_ck.access(i);

        if ((Long)r.get("return")>0)
        {
           System.out.println("CK error: "+r.get("error")+"!");
           System.exit(1);
        }

        String uid=(String) r.get("data_uid");
        System.out.println("CK UID: "+uid);

        System.exit(0);
    }
}
