#include "Tabla.h"

#include <time.h>
#include "Database.h"


void Tabela::fil(JobSqlResult& j)
{
    int time=clock();
    if(!j.bOk)
    {
        return;
    }
    LID=j.nLastInsertId;
    Names.reserve(j.res.num_fields());
    for(int a=0; a<j.res.num_fields(); a++)
    {
        string nam= j.res.field_name(a);
        transform(nam.begin(), nam.end(), nam.begin(), ::tolower);
        Names.push_back(nam);

        string qqq=j.res.field_type(a).name();
        transform(qqq.begin(), qqq.end(), qqq.begin(), ::toupper);
        if(qqq.find("DATE")!=-1)qqq="SS";
        if(qqq.find("TIME")!=-1)qqq="SS";
        if(qqq.find("SS")!=-1)qqq="SS";
        if(j.res.field(a).binary_type())
            qqq="BLOB";
        Types.push_back(qqq);
    }
    Linii.reserve(j.res.num_rows());
    for(int i=0; i<j.res.num_rows(); i++)
    {
        vector<string> temp;
        vector<Blob> tempb;
        for(int a=0; a<j.res.num_fields(); a++)
        {
            if(Types[a]=="BLOB")
            {
                temp.push_back(ss(tempb.size()));
                Blob blob;
                int64_t dim=(j.res[i][a]).length();
                blob.reserve(dim);
                for(int64_t b=0; b<dim; b++)
                {
                    blob.push_back(j.res[i][a][b]);
                }
                tempb.push_back(blob);
            }
            else
            {
                temp.push_back(j.res[i][a].c_str());
            }
        }
        Linie l;
        l.Results=temp;
        l.Blobs=tempb;
        l.Names=Names;
        Linii.push_back(l);
    }
}


Tabela::Tabela()
{
}
Tabela::Tabela(string Que)
{
    Fill(Que);
}
Tabela::~Tabela()
{
}
int Tabela::Count(string Column,string Value)
{
    int nr=0;
    for(int a=0; a<Linii.size(); a++)
        if(Linii[a][Column]==Value)
            nr++;
    return nr;
}
int Tabela::FindIndex(string Column,string Value)
{
    for(int a=0; a<Linii.size(); a++)
        if(Linii[a][Column]==Value)
            return a;
    return -1;///this should not happen
}
Linie& Tabela::Findy(string Column,string Value)
{
    for(int a=0; a<Linii.size(); a++)
        if(Linii[a][Column]==Value)
            return Linii[a];
    exit(420);
}
int Tabela::Col2(string col)
{
    transform(col.begin(), col.end(), col.begin(), ::tolower);
    for(int a=0; a<Names.size(); a++)
        if(Names[a]==col)
            return a;
    return -1;
}
void Tabela::Fill(string Que)
{
    LastQuery=Que;
    Linii.clear();
    Types.clear();
    Names.clear();
    JobSqlResult j=QueryToCall(Que,log);
    fil(j);
}
vector<string> Tabela::GetColumn(string x)
{
    return GetColumn(Col2(x));
}
vector<string> Tabela::GetColumn(int x)
{
    vector<string> ret;
    for(int a=0; a<Linii.size(); a++)
        ret.push_back(Linii[a][x]);
    return ret;
}
vector<unsigned char> Linie::getBlob(string x)
{
    Linie L=*this;
    return Blobs[atoi(L[x].c_str())];
}
string Blob2String(Blob B)
{
    return string(B.begin(), B.end());
}





string FileToDB(string name)
{
    if(name=="")return "''";
    FILE* f=fopen(name.c_str(),"rb");
    if(!f)
        return "";
    string ret="0x";
    uint8_t n;
    while(fread(&n,1,1,f)==1)
    {
        char tmp[3];
        sprintf(tmp, "%02X", n);
        ret+=tmp;
    }
    fclose(f);
    return ret;
}
bool DBToFile(Blob in, string name)
{
    FILE* f=fopen(name.c_str(),"wb");
    if(!f)
        return 0;
    for(int a=0; a<in.size(); a++)
    {
        uint8_t n = in[a];
        fwrite(&n,1,1,f);
    }
    fclose(f);
    return 1;
}