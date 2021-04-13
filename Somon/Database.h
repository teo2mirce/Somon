
#ifndef DATABASE_H
#define DATABASE_H
#include <mysql++.h>
#include <string>


#define ss(A) (({stringstream  da; da<<A; da.str();}))

using namespace mysqlpp;
using namespace std;
class SQL
{
    public:
    static const string IP,DB,User,Password;
    static const int port=3306;

};




int LogIn(string Email,string Parola);
//int InsetUser(wxString Email,wxString Parola);
//int UpdateUser(wxString Email,wxString Parola);
//wxString EncodePassword(const char* x);
//wxString RandCode();
int DBCheck();


struct JobSqlResult
{
    bool bOk;
    mysqlpp::StoreQueryResult res;
    int64_t nLastInsertId;
    string err;
};
JobSqlResult QueryToCall(string strQuery,bool log=1);


#endif // DATABASE_H
