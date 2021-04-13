/////https://github.com/ariya/phantomjs/blob/master/examples/waitfor.js
/////http://phantomjs.org/page-automation.html
/////https://superuser.com/questions/448514/command-line-browser-with-js-support
/////https://stackoverflow.com/questions/41777585/running-javascript-from-the-windows-command-prompt
#include <cstdio>
#include <iostream>
#include <memory>
#include <stdexcept>
#include <string>
#include <array>
#include <stdio.h>
extern "C" FILE *popen(const char *command, const char *mode);
extern "C" int pclose(FILE *stream);
using namespace std;
std::string exec(const char* cmd) {
    std::array<char, 128> buffer;
    std::string result;
    std::shared_ptr<FILE> pipe(popen(cmd, "r"), pclose);
    if (!pipe) throw std::runtime_error("popen() failed!");
    while (!feof(pipe.get())) {
        if (fgets(buffer.data(), 128, pipe.get()) != nullptr)
            result += buffer.data();
    }
    return result;
}


struct Edge
{
    string Source,Target,Port,ID;///sursa id, destinatie id, port
};
struct PortData
{
    string EdgeID,Value;
    PortData( string ID, string Val)
    {
        EdgeID=ID;
        Value=Val;
    }
};
struct Nod
{
    map<string,queue<PortData>> Port;///ce am in coada pentru fiecare "pipe" (pentru + am 2 pipe: int a, int b)
    string Type,Value,ID,OutType;
    vector<Edge> Outgoing;
    vector<Edge> Incoming;
};


struct Result
{
    string Rez;
    bool ok;
    Result( string R, bool o)
    {
        Rez=R;
        ok=o;
    }
};
