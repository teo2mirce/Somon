// *** ADDED BY HEADER FIXUP ***
#include <string>
// *** END ***
#ifndef MASA_H
#define MASA_H
#include <vector>
//#include <wx/string.h>
//#include <wx/arrstr.h>
//#include "stds.h"
#include<vector>
using namespace std;
typedef vector<uint8_t> Blob;
///#include <wx/msgdlg.h>

#include "Database.h"
#include <algorithm>
struct Linie
{
    vector<string> Results;
    vector<Blob> Blobs;
    vector<string> Names;
    string& operator[](int a)
    {
        return Results[a];
    };
    string& operator[](string col)
    {
        //col.MakeLower();
        transform(col.begin(), col.end(), col.begin(), ::tolower);

        for(int a=0; a<Names.size(); a++)
            if(Names[a]==col)
                return Results[a];
        exit(420);
    };

    bool operator <(const Linie& rhs) const
    {
        for(int a=0; a<Results.size(); a++)
            if(Results[a]!=rhs.Results[a])
                return Results[a]<rhs.Results[a];
        return 0;
    }

    Blob getBlob(string x);

};
#include <unordered_map>
#include <queue>
#include "Database.h"

struct Tabela
{
    bool log= {1};
    int64_t ID= {-1};

    unsigned long long LID;
    string LastQuery;
    vector<Linie> Linii;
    Tabela();
    ~Tabela();
    Tabela(string Que);
    void fil(JobSqlResult& j);
    int size()
    {
        return Linii.size();
    };
    void Fill(string Que);
    int Col2(string);
    Linie& operator[](int a)
    {
        if(a>=0 && a<Linii.size())
            return Linii[a];

        cout<<"Q: "<<LastQuery<<endl;
        cout<<"Line does not exist"<<endl;
        exit(420);
    };
    vector<string> operator[](string a)
    {
        return GetColumn(a);
    };
    Linie& Findy(string Col,string Value);
    int FindIndex(string Col,string Value);
    int Count(string Col,string Value);
    vector<string> Types;
    vector<string> Names;
    vector<string> GetColumn(int);
    vector<string> GetColumn(string);

};
string Blob2String(Blob B);
string FileToDB(string name);
bool DBToFile(Blob in, string name);
#endif // MASA_H

