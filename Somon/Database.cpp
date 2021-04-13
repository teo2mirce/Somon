#include "Database.h"
//#include "Misc.h"
const string SQL::IP="";
const string SQL::DB="";
const string SQL::User="";
const string SQL::Password="";


JobSqlResult QueryToCall(string strQuery,bool logg)
{
    int time=clock();
    JobSqlResult oResult;
    oResult.bOk=false;
    mysqlpp::Connection conn(false);
    conn.set_option(new mysqlpp::ConnectTimeoutOption(35));
    if (conn.connect(SQL::DB.c_str(), SQL::IP.c_str(),SQL::User.c_str(),SQL::Password.c_str(),SQL::port))
    {
        mysqlpp::Query query = conn.query();
        query<<(strQuery);
        oResult.res = query.store();
        if(strcmp(conn.error(),""))
        {
            cout<<"Q: "<<strQuery<<endl;
            cout<<query.error();
            exit(420);
        }
        else
        {
            oResult.bOk=true;
        }
        oResult.nLastInsertId=query.insert_id();
        conn.disconnect();
    }
    return oResult;
}

int LogIn(string Email,string Parola)
{
   Connection conn(false);
    if (conn.connect(SQL::DB.c_str(), SQL::IP.c_str(),SQL::User.c_str(),SQL::Password.c_str(),SQL::port))
    {
        Query query = conn.query();
        query<<"select * from users where Email='"<<escape<<Email<<"' and Password='"<<Parola<<"'";
        if (StoreQueryResult res = query.store())
        {
            if(res.num_rows())//email and pass exists
                return 1;
            query.reset();
            query<<"select * from users where Email='"<<escape<<Email<<"'";
            {
                StoreQueryResult res = query.store();
                if(res.num_rows())
                    return 2;//invalid password
                return 3;//just created, ask for email check
            }
        }
        else
        {
            return 0;
        }
    }
    else
    {
        return 0;
    }
    return 0;
}



int DBCheck()
{
    Connection conn(false);
    if (conn.connect(SQL::DB.c_str(), SQL::IP.c_str(),SQL::User.c_str(),SQL::Password.c_str(),SQL::port))
        return 1;
    return 0;
}
