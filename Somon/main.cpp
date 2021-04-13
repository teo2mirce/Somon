#include <iostream>
#include "Tabla.h"
#include "Database.h"
#include <wchar.h>

#include "main.h"

#include "json/json11.hpp"
using namespace json11 ;
//https://github.com/dropbox/json11


#include <stdlib.h>
#include <stdio.h>
#include <errno.h>
#include <fstream>

using namespace std;


vector<string> Split(string str,string delimiter)///str Se schimba
{
    vector<string> R;
    size_t pos = 0;
    string token;
    while ((pos = str.find(delimiter)) != string::npos) {
        token = str.substr(0, pos);
        R.push_back(token);
        str.erase(0, pos + delimiter.length());
    }
    R.push_back(str);
    return R;
}
vector<string> SplitOnce(string& str,string delimiter)
{
    vector<string> result;
    int f=str.find(delimiter);
    result.push_back(str.substr(0, f));
    result.push_back(str.substr(f+delimiter.length()));
    return result;
}
//#define DATA map<string,string>
class DATA
{
    public:

    string Get(string i)
    {
        assert(RealData.find(i)!=RealData.end());


//        if(SplitOnce(i," ")[0]=="Int")
//            assert(isNumber(RealData[i]));
        return RealData[i];
    }
    void Set(string i,string v)
    {
        RealData[i]=v;
    }
    map<string,string> RealData;
};
//void GetFunctionDef(string& InVar,map<string,string>& FunctionDef)
//{
//    vector<string> Q=Split(InVar,"\r\n");
//    for(int a=0;a<Q.size();a++)
//    {
//        vector<string> C=SplitOnce(Q[a]," ");
//        FunctionDef[C[1]]=C[0];
//    }
//}
bool FileExists (const std::string& name) {
    if (FILE *file = fopen(name.c_str(), "r")) {
        fclose(file);
        return true;
    } else {
        return false;
    }
}

#include <iostream>
#include <string>

map<string,int> DefaultFunctionUsed;
map<string,int> PublicFunctionUsed;
//f
struct FunctionDefinition
{
    vector<string> In;
    string OutType;

    ///pentru public doar
    string OutVar;
    Json json;
};


Json Str2Json(string &str)
{
    if(str.length()<2)
        exit(420);
    str = str.substr(1, str.size() - 2);///primul si ultimul char e '
    string err;
    return Json::parse(str, err);
}


struct SomonDeRezolvat
{
    string PublicFunctionID;
    map<string,Nod> Noduri;
    map<string,string> Name2ID;
    vector<string> NodOut;
    SomonDeRezolvat(){};
    SomonDeRezolvat(Json& json,string& Out,string ID);
    void FillIn(const string& In);
    Result Solve(string& In,string& InstanceStarterID);
};

map<string,FunctionDefinition> ClassDefinitions;
map<string,FunctionDefinition> DefaultDefinitions;
map<string,FunctionDefinition> PublicDefinitions;

map<string,SomonDeRezolvat> SDRS;

void LoadDefault()
{
    Tabela Tab("select ID,InVar,OutType from DefaultFunctions");
    for(int a=0;a<Tab.size();a++)
    {
        FunctionDefinition FD;
        if(Tab[a]["InVar"]!="")
            FD.In=Split(Tab[a]["InVar"],",");
        FD.OutType=Tab[a]["OutType"];
        DefaultDefinitions[ Tab[a]["ID"] ]=FD;
    }

}
SomonDeRezolvat::SomonDeRezolvat(Json& json,string& Out,string ID)
{
    PublicFunctionID=ID;
    for(auto &i : json["elements"]["nodes"].array_items())
    {
        Nod n;
        n.Type=i["data"]["Type"].string_value();
        n.Value=i["data"]["Value"].string_value();
        n.ID=i["data"]["id"].string_value();
        n.OutType=i["data"]["OutType"].string_value();
        Noduri[i["data"]["id"].string_value()]=n;
    }
    for(auto &i : json["elements"]["edges"].array_items())
    {
        Edge e;
        e.Port=i["data"]["Port"].string_value();
        e.ID=i["data"]["id"].string_value();
        e.Source=i["data"]["source"].string_value();
        e.Target=i["data"]["target"].string_value();
        Noduri[e.Source].Outgoing.push_back(e);
        Noduri[e.Target].Incoming.push_back(e);
    }
    for(auto it: Noduri)
        if(it.second.Type=="Variable")
            Name2ID[it.second.Value]=it.first;

    vector<string> OutComplet=Split(Out,",");
    for(int a=0;a<OutComplet.size();a++)
        NodOut.push_back(Name2ID[SplitOnce(OutComplet[a]," ")[1]]);
    ///ca sa vezi date
//            cout<<json["elements"]["nodes"].dump()<<endl;
//            cout<<json["elements"]["edges"].dump()<<endl;
////            cout<<json.dump()<<endl;
//            return 0;

}
void LoadPublic()
{
    Tabela Tab("select ID,InVar,OutType,OutVar,JsonString from PublicFunctions");
    for(int a=0;a<Tab.size();a++)
    {
        FunctionDefinition FD;
        string InVar=Tab[a]["InVar"];
        if(InVar!="")
            FD.In=Split(InVar,",");
        FD.OutType=Tab[a]["OutType"];
        FD.OutVar=Tab[a]["OutVar"];
        FD.json=Str2Json(Tab[a]["JsonString"]);
        PublicDefinitions[ Tab[a]["ID"] ]=FD;
        SomonDeRezolvat SDR(FD.json,FD.OutVar,Tab[a]["ID"]);
        SDRS[Tab[a]["ID"]] = SDR;
    }
}
void LoadClasses()
{
    Tabela Tab("select ID,InVar,concat(Name,'(',ID,')') OutType from Classes");
    for(int a=0;a<Tab.size();a++)
    {
        FunctionDefinition FD;
        if(Tab[a]["InVar"]!="")
            FD.In=Split(Tab[a]["InVar"],",");
        FD.OutType=Tab[a]["OutType"];
        ClassDefinitions[ Tab[a]["ID"] ]=FD;
    }
}
void LoadFromDB()
{
    cout<<"Loading defaults..."<<endl;
    LoadDefault();
    cout<<"DONE"<<endl;
    cout<<"Loading publics..."<<endl;
    LoadPublic();
    cout<<"DONE"<<endl;
    cout<<"Loading classes..."<<endl;
    LoadClasses();
    cout<<"DONE"<<endl;
}

bool StringToBool(string Str){assert(Str=="True" || Str=="False"); return Str=="True";}
string BoolToString(bool bl){return bl?"True":"False";}

#include <stdlib.h>
#include <cstring>


#include "Link2Dom.h"
#include <dirent.h>
void DeleteDir(string d)
{
    string Q="rmdir /Q /S "+d;
    system(Q.c_str());
    return;
    DIR *theFolder = opendir(d.c_str());
    struct dirent *next_file;
    char filepath[256];

    while ( (next_file = readdir(theFolder)) != NULL )
    {
        // build the path for each file in the folder
        sprintf(filepath, "%s/%s", d.c_str(), next_file->d_name);
        remove(filepath);
    }
    closedir(theFolder);
}

//#include "InfInt.h"
#define InfInt(A) atoi(A.c_str())
Linie SaveFile(string ID)
{
    mkdir("res");
    Tabela T("select BlobFile,FileName,UserID from BlobFiles where ID="+ID);
    Linie L=T[0];
    DBToFile(L.Blobs[0],"res/"+L["FileName"]);
    return L;
}

void CleanString(string &str)
{
    for(int a=0;a<str.size();a++)
    {
        ///doar alfa numeric, ! si ?
        char c=str[a];
        if(c!=' ')
        if(c!='!')
        if(c!='?')
        if(!('a'<=c && c<='z') && !('A'<=c && c<='Z') && !('0'<=c && c<='9'))
            str[a]=' ';
    }
}

string SolveDefault(string& Functie,DATA& data,string& InstanceStarterID)
{
//    Sleep(123);
    if(Functie=="1")///plus
        return ss(InfInt(data.Get("Int a"))+InfInt(data.Get("Int b")));
    if(Functie=="2")///minus
        return ss(InfInt(data.Get("Int a"))-InfInt(data.Get("Int b")));
    if(Functie=="3")///modul
        return ss(InfInt(data.Get("Int a"))%InfInt(data.Get("Int b")));
    if(Functie=="4")///--
        return ss(InfInt(data.Get("Int a"))-1);
    if(Functie=="5")///++
        return ss(InfInt(data.Get("Int a"))+1);
    ///6=for
    if(Functie=="7")///==0
        return BoolToString(InfInt(data.Get("Int a"))==0);
    if(Functie=="8")///or
        return BoolToString((StringToBool(data.Get("Bool a")) or StringToBool(data.Get("Bool b"))));
    if(Functie=="9")///and
        return BoolToString((StringToBool(data.Get("Bool a")) and StringToBool(data.Get("Bool b"))));
    ///10 = if
    if(Functie=="11")///ifte
    {
        if(data.Get("Bool cond")=="True")
            return data.Get("Int then");
        else
            return data.Get("Int else");
    }
    if(Functie=="12")/// >
        return BoolToString(InfInt(data.Get("Int a"))>InfInt(data.Get("Int b")));
    if(Functie=="13")/// <
        return BoolToString(InfInt(data.Get("Int a"))<InfInt(data.Get("Int b")));

    if(Functie=="14")///WavToMp3
    {
        Linie L;
        L=SaveFile(data.Get("Wav in"));

        string Mp3=SplitOnce(L["FileName"],".")[0]+".mp3";
        exec(ss("Exes\\ffmpeg.exe -i \"res/"+L["FileName"]+"\" -vn -ar 44100 -ac 2 -b:a 192k  \"res/"+Mp3+"\"").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+Mp3)+",'"+Mp3+"',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }


    if(Functie=="15")/// ==Int
        return BoolToString(InfInt(data.Get("Int a"))==InfInt(data.Get("Int b")));
    if(Functie=="16")/// /
        return ss(InfInt(data.Get("Int a"))/InfInt(data.Get("Int b")));

    if(Functie=="17")/// itoa
        return data.Get("Int a");
    if(Functie=="18")/// atoi
        return data.Get("String a");
    if(Functie=="19")/// strrev
    {
        string Copie=data.Get("String a");
        reverse(Copie.begin(), Copie.end());
        return Copie;
    }
    if(Functie=="20")/// ==Str
        return BoolToString(data.Get("String a")==data.Get("String b"));

    if(Functie=="21")///*
        return ss(InfInt(data.Get("Int a"))*InfInt(data.Get("Int b")));

    if(Functie=="22")///JsOnUrl
    {
        ofstream fout("Exes\\js.js");
        fout<<"var system = require('system');var page = require('webpage').create();page.open(system.args[1], function(status){var ua = page.evaluate(function() {return     "+data.Get("String Js")+"     });console.log(ua);phantom.exit();});";
        fout.close();
        return (exec(ss("Exes\\phantomjs.exe Exes\\js.js "+data.Get("String URL")).c_str()));
    }

    if(Functie=="23")///Head
    {
        string List=data.Get("[Int] List");
        List = List.substr(1, List.size() - 2);///primul si ultimul char e [ si ]
        string Head=SplitOnce(List,",")[0];
        return Head;
    }
    if(Functie=="24")///GetPalette
    {
        Linie L=SaveFile(data.Get("Image img"));
        exec(ss("Exes\\magick.exe \"res/"+L["FileName"]+"\" -geometry 16x16 -colors "+data.Get("Int colors")+" -unique-colors -scale 4000% res/scheme.png").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/scheme.png")+",'scheme.png',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }
    if(Functie=="25" || Functie=="26")///Scale
    {
        Linie L;
        if(Functie=="25")
            L=SaveFile(data.Get("Image img"));
        else
            L=SaveFile(data.Get("Video vid"));
        exec(ss("Exes\\ffmpeg.exe -i \"res/"+L["FileName"]+"\" -vf   \"scale=ceil(iw*0.01*"+data.Get("Int percent")+"*0.5)*2:-2\"  \"res/_"+L["FileName"]+"\"").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/_"+L["FileName"])+",'"+L["FileName"]+"',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }
    if(Functie=="27")///..
    {
        int mi=InfInt(data.Get("Int a"));
        int ma=InfInt(data.Get("Int b"));
        if(mi==ma)
            return "["+ss(mi)+"]";
        if(mi>ma)
            return "[]";

        string ret="[";
        for(int II=mi;II<ma;II++)
            ret+=ss(II)+",";
        ret+=ss(ma)+"]";
        return ret;
    }
    if(Functie=="28")///Gif reverse
    {
        Linie L=SaveFile(data.Get("Gif gif"));
        exec(ss("Exes\\magick.exe \"res/"+L["FileName"]+"\" -reverse \"res/_"+L["FileName"]+"\"").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/_"+L["FileName"])+",'"+L["FileName"]+"',"+InstanceStarterID+")").nLastInsertId);
        DeleteDir("res");
        return ID;
    }
    if(Functie=="29")///Not
        return BoolToString( ! StringToBool(data.Get("Bool a")) );

    if(Functie=="30")/// !=Int
        return BoolToString(InfInt(data.Get("Int a"))!=InfInt(data.Get("Int b")));

    if(Functie=="31")///concat Int
    {
        string a=data.Get("[Int] a");
        string b=data.Get("[Int] b");
        if(a=="[]")return b;
        if(b=="[]")return a;

        a.pop_back();
        b.erase(0, 1);
        return a+","+b;
    }
    if(Functie=="32")///Datetime
    {
         time_t rawtime;
          struct tm * timeinfo;
          char buffer[80];

          time (&rawtime);
          timeinfo = localtime(&rawtime);

          strftime(buffer,sizeof(buffer),"%Y-%m-%d %H:%M:%S",timeinfo);
          return ss(buffer);
    }

    if(Functie=="33")///Mp3ToWav
    {
        Linie L;
        L=SaveFile(data.Get("Mp3 in"));

        string Wav=SplitOnce(L["FileName"],".")[0]+".wav";
        exec(ss("Exes\\ffmpeg.exe -i \"res/"+L["FileName"]+"\" -acodec pcm_s16le -ac 1 -ar 16000  \"res/"+Wav+"\"").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+Wav)+",'"+Wav+"',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }
    if(Functie=="34")///WavToMidi
    {
        Linie L;
        L=SaveFile(data.Get("Wav in"));

        string Mid=SplitOnce(L["FileName"],".")[0]+".mid";
        exec(ss("Exes\\waon\\waon.exe -i \"res/"+L["FileName"]+"\" -o \"res/"+Mid+"\"").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+Mid)+",'"+Mid+"',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }
    if(Functie=="35")///MidiToSheets
    {
        Linie L;
        L=SaveFile(data.Get("Mid in"));

        string Name=SplitOnce(L["FileName"],".")[0];
        exec(ss("Exes\\sheet.exe \"res/"+L["FileName"]+"\" \"res/"+Name+"\"").c_str());


        string Ret="[";
        bool MaiAm=1;
        for(int a=1;MaiAm;a++)
        {
            string Fisier=ss(Name<<"_"<<a<<".png");
            if(FileExists("res//"+Fisier))
            {
                string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+Fisier)+",'"+Fisier+"',"+InstanceStarterID+")").nLastInsertId);
                Ret+=ss(ID<<",");
            }
            else
                MaiAm=0;
        }
        if(Ret!="[")
            Ret.pop_back();
        Ret+="]";
        DeleteDir("res");
        return Ret;
    }
    if(Functie=="36")///AudioAndImageToVideo
    {
        Linie LImg=SaveFile(data.Get("Image img"));
        Linie LMp3=SaveFile(data.Get("Mp3 mp3"));

        exec(ss("Exes\\ffmpeg.exe -loop 1 -i \"res/"+LImg["FileName"]+"\" -i \"res/"+LMp3["FileName"]+"\" -vf \"scale=ceil(iw*0.5)*2:-2\"  -c:a copy -c:v libx264 -shortest res/out.mp4 ").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/out.mp4")+",'out.mp4',"+InstanceStarterID+")").nLastInsertId);


        DeleteDir("res");
        return ID;
    }
    if(Functie=="37")///StringToWav
    {
        mkdir("res");

        string text=data.Get("String in");
        CleanString(text);

        exec(ss("Exes\\balcon\\balcon.exe -t \" "+text+" \" -w  res/out.wav").c_str());


        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/out.wav")+",'out.wav',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }
    if(Functie=="38")///IntToList
        return "["+data.Get("Int a")+"]";
    if(Functie=="39")/// ==Int
        return BoolToString(data.Get("[Int] a")==data.Get("[Int] b"));

    ///40 e If[Int]

    if(Functie=="41")///Tail
    {
        string List=data.Get("[Int] List");
        string Out="";
        if(List=="[]" || List.find(',')==-1)
            Out="[]";
        else
            Out="["+SplitOnce(List,",")[1];
        return Out;
    }

    if(Functie=="42")///OCR
    {
        Linie L=SaveFile(data.Get("Image img"));

        exec(ss("Exes\\tesseract\\tesseract.exe  \"res/"+L["FileName"]+"\" res/da").c_str());///dc pui res/da.txt se face res/da.txt.txt   tampiti

        ifstream ifs("res/da.txt");///https://stackoverflow.com/questions/2912520/read-file-contents-into-a-string-in-c
        string content( (istreambuf_iterator<char>(ifs) ), (istreambuf_iterator<char>()    ) );
        ifs.close();


        CleanString(content);

        DeleteDir("res");
        return content;
    }

    if(Functie=="43")///PdfToImages
    {
        Linie L;
        L=SaveFile(data.Get("Pdf in"));

        string Name=SplitOnce(L["FileName"],".")[0];
        exec(ss("Exes\\gs\\gswin32c.exe  -sDEVICE=jpeg -q -o res/figure-%03d.jpg -r144 \"res/"+L["FileName"]+"\"").c_str());

        string Ret="[";
        bool MaiAm=1;
        for(int a=1;MaiAm;a++)
        {
            char temp[400];
            sprintf(temp,"figure-%03d.jpg",a);
            string Fisier=ss(temp);
            if(FileExists("res//"+Fisier))
            {
                string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+Fisier)+",'"+Fisier+"',"+InstanceStarterID+")").nLastInsertId);
                Ret+=ss(ID<<",");
            }
            else
                MaiAm=0;
        }
        if(Ret!="[")
            Ret.pop_back();
        Ret+="]";
        DeleteDir("res");
        return Ret;
    }

    if(Functie=="44")///Train SVM
    {
        Linie L;
        L=SaveFile(data.Get("CSV in"));

        string SVM=SplitOnce(L["FileName"],".")[0]+".SVM";
        exec(ss("Exes\\SVM\\test.exe \"res/"+L["FileName"]+"\" >  res/temp.temp").c_str());
        exec(ss("Exes\\SVM\\svm-train.exe res/temp.temp  \"res/"+SVM+"\"").c_str());


        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+SVM)+",'"+SVM+"',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }


    if(Functie=="45")///Predict SVM
    {

        Linie L1=SaveFile(data.Get("CSV in"));
        Linie L2=SaveFile(data.Get("SVM trained"));

        string Txt=SplitOnce(L1["FileName"],".")[0]+".txt";
        exec(ss("Exes\\SVM\\test.exe \"res/"+L1["FileName"]+"\" >  res/temp.temp").c_str());
        exec(ss("Exes\\SVM\\svm-predict.exe res/temp.temp  \"res/"+L2["FileName"]+"\" \"res/"+Txt+"\"   ").c_str());

        ///svm-predict.exe data2.test data.Trained data.out

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+Txt)+",'"+Txt+"',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }

    ///https://www.7-zip.org/download.html
    if(Functie=="46")///Kernel
    {
        Linie L=SaveFile(data.Get("Zip in"));

        ///dezarhivam
        string Zip=SplitOnce(L["FileName"],".")[0]+".Zip";
        exec(ss("Exes\\7za.exe x -ores \"res/"+Zip+"\"").c_str());

        ///rulam exe
        exec(ss("Exes\\Python\\python.exe Exes\\KC.py res >  res/rez.txt").c_str());


        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/rez.txt")+",'rez.txt',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");
        return ID;
    }

    if(Functie=="47")///WSD tldr
    {
        string str=data.Get("String sentence");
        CleanString(str);
        string content=exec(ss("Exes\\Python\\python.exe Exes\\WSD.py TLDR \""+str+"\" "+data.Get("String word")).c_str());
        return content;
    }
    if(Functie=="48")///WSD Show
    {

        mkdir("res");

        string str=data.Get("String sentence");
        CleanString(str);

        exec(ss("Exes\\Python\\python.exe Exes\\WSD.py Show \""+str+"\" "+data.Get("String word") +" >  res/rez.txt").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/rez.txt")+",'rez.txt',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");

        return ID;
    }

    if(Functie=="49")///ImageFeatures:     Image img -> [Double]
    {
        Linie L=SaveFile(data.Get("Image img"));

        string Ret=exec(ss("Exes\\Python\\python.exe Exes\\ImageFeatures.py \"res/"+L["FileName"]+"\"").c_str());

        DeleteDir("res");
        return "["+Ret+"]";
    }
    if(Functie=="50")///MachineLearning    String Alg,String Norm,CSV Train,CSV Test -> Txt
    {
        Linie Train=SaveFile(data.Get("CSV Train"));
        Linie Test=SaveFile(data.Get("CSV Test"));

        exec(ss("Exes\\Python\\python.exe Exes\\TryAllShowBest.py \"res/"+Train["FileName"]+"\"  \"res/"+Test["FileName"]+"\"  \""+data.Get("String Alg") +"\"  "+data.Get("String Norm") +"   >  res/rez.txt").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/rez.txt")+",'rez.txt',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");

        return ID;
    }
    if(Functie=="51")///EverythingSolver  CSV Train,Int sec -> Txt
    {

        Linie L=SaveFile(data.Get("CSV Train"));

        exec(ss("Exes\\Python\\python.exe Exes\\EverythingSolver.py \"res/"+L["FileName"]+"\" "+data.Get("Int sec") +" >  res/rez.txt").c_str());

        string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/rez.txt")+",'rez.txt',"+InstanceStarterID+")").nLastInsertId);

        DeleteDir("res");

        return ID;
    }
	 if(Functie=="52")///Histograms  CSV Train -> [Images]
    {

        Linie L=SaveFile(data.Get("CSV Train"));

        exec(ss("Exes\\Python\\python.exe Exes\\HistPlot.py \"res/"+L["FileName"]+"\" res/Imgs").c_str());

        string Ret="[";
        for (const auto & entry :get_all_files_names_within_folder("res/Imgs"))///https://stackoverflow.com/questions/612097/how-can-i-get-the-list-of-files-in-a-directory-using-c-or-c
        {
            string Fisier=ss("Imgs/"<<entry);

            string ID=ss(QueryToCall("INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ("+FileToDB("res/"+Fisier)+",'"+entry+"',"+InstanceStarterID+")").nLastInsertId);
            Ret+=ss(ID<<",");
        }
        if(Ret!="[")
            Ret.pop_back();
        Ret+="]";
        DeleteDir("res");
        return Ret;
    }


    cout<<"Error: "<<Functie<<endl;
    exit(420);
}

bool AmTot(Nod & nod,FunctionDefinition &FD)
{
//    cout<<"In am tot pt "<<Nod.ID<<endl;
    for(int a=0;a<FD.In.size();a++)
        if(nod.Port[FD.In[a]].size()==0)
        {
//            cout<<"Nu am <"<<FD.In[a]<<">"<<endl;
            return false;
        }
    return true;
}

//void PrintNode(Nod &nod)
//{
//    cout<<"---------------"<<endl;
//    for(auto &it : nod.Am)
//        cout<<it.first<<" "<<it.second.size()<<endl;
//    cout<<"---------------"<<endl;
//}
int tot;
void SomonDeRezolvat::FillIn(const string& In)
{
    int c;
    ///era bug ca nu clearuiam ce aveau nodurile pe porturi si dadea chestii dubioase
    for(auto &it : Noduri)
        it.second.Port.clear();

    vector<string> InVars=Split(In,"\n");
    vector<string> Var;
    //int VarSize=InVars.size();
    c=clock();
    ///250.000 pt primele 1000 nr prime => cam 3 sec doar de aici pana la sf functiei
    for(string& VarAuto:InVars)
    {
        Var=SplitOnce(VarAuto,"=");///int out=3 sau img in=3
        string& ID=Name2ID[SplitOnce(Var[0]," ")[1]];
        Noduri[ ID ].Port[""].push(PortData("",Var[1]));
    }
    tot+=clock()-c;
}

Result SomonDeRezolvat::Solve(string& In,string& InstanceStarterID)
{
//    cout<<"In Solve: "<<PublicFunctionID<<endl<<"!"<<In<<"!"<<endl<<"EOSOLVE"<<endl;
    FillIn(In);
//        unsigned long MAX_ALLOWED_CYCLES=6664,now=0;
    while(1)
    {
//            now++;
//            if(now>MAX_ALLOWED_CYCLES)
//                return Result("",0);
        for(int a=0;a<NodOut.size();a++)
            if(Noduri[NodOut[a]].Port[""].size())
            {
//                cout<<"!"<<Noduri[NodOut[a]].Port[""].front().Value<<"!"<<endl;
                return Result(Noduri[NodOut[a]].Port[""].front().Value,1);
            }
        bool AmFacutCeva=0;
        for(auto &it : Noduri)
        {
            vector<Edge>& Out=it.second.Outgoing;
            if(Out.size())///are noduri de iesire
            {
                Nod& nod=it.second;


                if(nod.Type=="Default")///Functie
                {
                    FunctionDefinition FD=DefaultDefinitions[nod.Value];
                    if(AmTot(nod,FD))
                    {
                        AmFacutCeva=1;
                        DefaultFunctionUsed[nod.Value]++;

                        DATA data;
                        for(int a=0;a<FD.In.size();a++)
                        {
                            string AIDI=FD.In[a];
                            data.Set(AIDI,nod.Port[AIDI].front().Value);
                            nod.Port[AIDI].pop();
                        }
                        string Val=SolveDefault(nod.Value,data,InstanceStarterID);
                        for(int a=0;a<Out.size();a++)
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,Val));

//                            if(nod.Value=="6")///for
//                            {
//                                int mi=InfInt(data.Get("Int a"));
//                                int ma=InfInt(data.Get("Int b"));
//                                for(int II=mi;II<=ma;II++)
//                                    for(int a=0;a<Out.size();a++)
//                                        Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,ss(II)));
//                            }
//                            cout<<"OUT DEF"<<endl;
                    }
//                        exit(69);
                }
                if(nod.Type=="Class")
                {
                    FunctionDefinition FD=ClassDefinitions[nod.Value];
                    if(AmTot(nod,FD))
                    {
                        AmFacutCeva=1;

                        string InVar;
                        for(int a=0;a<FD.In.size();a++)
                        {
                            string AIDI=FD.In[a];
                            InVar+=FD.In[a]+"="+nod.Port[AIDI].front().Value+"\n";
                            nod.Port[AIDI].pop();
                        }
                        for(int a=0;a<Out.size();a++)
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,InVar));
                    }
                }
                if(nod.Type=="Public")
                {
                    FunctionDefinition FD=PublicDefinitions[nod.Value];
                    if(AmTot(nod,FD))
                    {
                        AmFacutCeva=1;
                        PublicFunctionUsed[nod.Value]++;
                        string InVar;
                        for(int a=0;a<FD.In.size();a++)
                        {
                            string AIDI=FD.In[a];
                            InVar+=FD.In[a]+"="+nod.Port[AIDI].front().Value+"\n";
                            nod.Port[AIDI].pop();
                        }
                        Result Rez=SDRS[nod.Value].Solve(InVar,InstanceStarterID);


//                        cout<<endl<<endl<<endl;
//                        cout<<"---------------"<<endl;
//                        cout<<nod.Value<<": "<<InVar<<"=>"<<Rez.Rez;
//                        cout<<"---------------"<<endl;;
//                        cout<<endl<<endl<<endl;


                        if(Rez.ok==0)
                            return Result("",0);
                        for(int a=0;a<Out.size();a++)
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,Rez.Rez));

                    }
                }
                if(nod.Type=="Filter")
                {
                    string PublicID=SplitOnce(nod.Value," ")[0];
                    string FieldVechi=SplitOnce(nod.Value," ")[1];
                    string FieldNou="["+SplitOnce(FieldVechi," ")[0]+"] "+SplitOnce(FieldVechi," ")[1];

                    FunctionDefinition FDOrig=PublicDefinitions[PublicID];
                    FunctionDefinition FDModificat=FDOrig;
                    for(int a=0;a<FDModificat.In.size();a++)
                        if(FDModificat.In[a]==FieldVechi)
                            FDModificat.In[a]=FieldNou;
                    if(AmTot(nod,FDModificat))
                    {

                        AmFacutCeva=1;

                        PublicFunctionUsed[PublicID]++;



                        string InVar;
                        for(int a=0;a<FDOrig.In.size();a++)
                        if(FDOrig.In[a]!=FieldVechi)
                        {
                            string AIDI=FDOrig.In[a];
                            InVar+=FDOrig.In[a]+"="+nod.Port[AIDI].front().Value+"\n";
                            nod.Port[AIDI].pop();
                        }

                        string List=nod.Port[FieldNou].front().Value;
                        nod.Port[FieldNou].pop();
                        List = List.substr(1, List.size() - 2);///primul si ultimul char e [ si ]
                        string Res="[";
                        vector<string> Vals=Split(List,",");
                        if(List!="")
                        for(int a=0;a<Vals.size();a++)
                        {
                            string InVar2=InVar+FieldVechi+"="+Vals[a]+"\n";
                            Result Rez=SDRS[PublicID].Solve(InVar2,InstanceStarterID);
//                            cout<<PublicID<<" IN: "<<InVar2<<endl<<"OUT: "<<Rez.Rez<<" => "<<Vals[a]<<endl;
                            if(Rez.ok==0)
                                return Result("",0);
                            if(Rez.Rez=="True")
                                Res+=Vals[a]+",";
                        }
                        if(Res!="[")
                            Res.pop_back();
                        Res+="]";



                        for(int a=0;a<Out.size();a++)
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,Res));
                    }
                }
                if(nod.Type=="ClassGetter")
                {
                    if(nod.Port[""].size())
                    {
                        AmFacutCeva=1;
                        string Field=SplitOnce(nod.Value," ")[1];
                        string Val1=nod.Port[""].front().Value;
                        nod.Port[""].pop();
                        vector<string> Fields=Split(Val1,"\n");
                        string Val2="";
                        for(int a=0;a<Fields.size();a++)
                            if(SplitOnce(Fields[a],"=")[0]==Field)
                                Val2=SplitOnce(Fields[a],"=")[1];
                        for(int a=0;a<Out.size();a++)
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,Val2));
                    }
                }
                if(nod.Type=="Variable")
                {
                    if(nod.Port[""].size())
                    {
                        AmFacutCeva=1;
                        string Val=nod.Port[""].front().Value;
                        nod.Port[""].pop();
                        for(int a=0;a<Out.size();a++)
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,Val));
                    }
                }
                if(nod.Type=="IF")
                {
                    FunctionDefinition FD;
                    FD.OutType=nod.OutType;
                    string Port=nod.OutType+" a";
                    FD.In={"Bool cond",Port};
                    if(AmTot(nod,FD))
                    {
                        AmFacutCeva=1;
                        string Cond=nod.Port["Bool cond"].front().Value;
                        string Val=nod.Port[Port].front().Value;
                        nod.Port["Bool cond"].pop();
                        nod.Port[Port].pop();
                        if(Cond=="True")
                            for(int a=0;a<Out.size();a++)
                                Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,Val));
                    }
                }
                if(nod.Type=="Constant")
                    for(int a=0;a<Out.size();a++)
                        if(Noduri[Out[a].Target].Port[Out[a].Port].size()==0)
                        {
                            AmFacutCeva=1;
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,nod.Value));
                        }


                if(nod.Type=="Recursive" && AmFacutCeva==0)
                {
                    if(nod.Port[""].size())
                    {
                        AmFacutCeva=1;
                        string Val=nod.Port[""].front().Value;

//                            cout<<Val<<endl;
//                            Sleep(1234);

                        nod.Port[""].pop();
                        FunctionDefinition FD=PublicDefinitions[PublicFunctionID];
                        PublicFunctionUsed[PublicFunctionID]++;
                        string InVar=FD.In[0]+"="+Val;

//                            cout<<"Rec: "<<InVar<<" ?? "<<endl;
                        Result Rez=SDRS[PublicFunctionID].Solve(InVar,InstanceStarterID);
                        if(Rez.ok==0)
                            return Result("",0);
                        for(int a=0;a<Out.size();a++)
                            Noduri[Out[a].Target].Port[Out[a].Port].push(PortData(Out[a].ID,Rez.Rez));

//                            cout<<"Rec: "<<InVar<<" TO "<<Rez.Rez<<endl;
                    }
                }
            }

//                cout<<"OUT"<<endl;
        }

        if(AmFacutCeva==0)
            return Result("Nothing changed",0);


    }
//        cout<<"Out Solve"<<endl;
}


string DiffClockStr( clock_t clock1, clock_t clock2 ) {

    double diffticks = clock1 - clock2;
    double diffms    = diffticks / ( CLOCKS_PER_SEC / 1000 );

    ostringstream strs;
    strs << diffms;
    return strs.str();
}
int main()
{

    assert(DBCheck()==1);
    ifstream fin("config.uap");
    string Email;
    fin>>Email;
    string Password;
    fin>>Password;
    if (Email.find('\'') != -1 || Password.find('\'') != -1)
        return 3;

    cout<<"Email: "<<Email<<endl;

    Tabela User("select ID,SolveInstancesFor from Users where Email='"+Email+"' and Password='"+Password+"'");
    assert(User.size()==1);
    string UID=User[0][0];
    string Where;
    if(User[0][1]=="All")
        Where=" (ForUser=0 or ForUser="+UID+") ";
    else
        Where=" ForUser="+UID+" ";

    while(1)
    {
        Tabela Instances;
        Instances.Fill("select * from Instances where "+Where+" and State='Idle' limit 1");

        if(Instances.size())
        {
            LoadFromDB();
            QueryToCall("Update Instances set State='Taken' where ID="+Instances[0]["ID"]);

            DefaultFunctionUsed.clear();
            PublicFunctionUsed.clear();

            PublicFunctionUsed[Instances[0]["F"]]++;

            FunctionDefinition FD=PublicDefinitions[Instances[0]["F"]];

            clock_t Start=clock();
            tot=0;
            Result Rez=SDRS[Instances[0]["F"]].Solve(Instances[0]["InVar"],Instances[0]["Starter"]);
            clock_t End=clock();

            string TimeDif=DiffClockStr(End,Start);

            replace (Rez.Rez.begin(), Rez.Rez.end(), '\'' , '`');///don't stop me now


            map<string,int> EdgeSize;///EdgeID -> Cate

            for(auto &it : SDRS[Instances[0]["F"]].Noduri)
            {
                Nod& nod=it.second;
                for(auto& ti : nod.Port)
                {
                    queue<PortData> &ti2=ti.second;
                    while(ti2.size())
                    {
                        PortData P=ti2.front();
                        ti2.pop();
                        EdgeSize[P.EdgeID]++;
                    }
                }
            }
            string EdgeSizes;
            for(auto &it : EdgeSize)
                if(it.first!="")
                    EdgeSizes+=ss(it.first<<"="<<it.second<<",");
            if(EdgeSizes!="")
                EdgeSizes.pop_back();

            string State="Fail";
            if(Rez.ok)
                State="Done";
            QueryToCall("Update Instances set State='"+State+"',OutValue='"+Rez.Rez+"',Worker="+UID+" ,Duration="+TimeDif+", EdgeSizes='"+EdgeSizes+"' where ID="+Instances[0]["ID"]);
            QueryToCall("Update Users set SC=SC+("+TimeDif+"/60000.0) where ID="+UID);

            {
                string Def="('Default',-1,1)";
                for(auto &i : DefaultFunctionUsed)
                    Def+=",('Default',"+i.first+","+ss(i.second)+")";
                QueryToCall("Insert into FunctionUsage(Type,ID,Nr) values "+Def+" ON DUPLICATE KEY UPDATE Nr=Nr+ VALUES(Nr)");

                string Pub="('Public',-1,1)";
                for(auto &i : PublicFunctionUsed)
                    Pub+=",('Public',"+i.first+","+ss(i.second)+")";
                QueryToCall("Insert into FunctionUsage(Type,ID,Nr) values "+Pub+" ON DUPLICATE KEY UPDATE Nr=Nr+ VALUES(Nr)");
            }
            cout<<"NEXT! tot:"<<tot<<endl;

        }
        else
            Sleep(15000);
        cout<<"Waiting..."<<endl;
    }
}



