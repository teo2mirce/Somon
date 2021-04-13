




//string Link2Dom(string x,string page)
//{
//    WSADATA wsaData;
//
//    if (WSAStartup(MAKEWORD(2,2), &wsaData) != 0)
//		return "";
//
//	SOCKET Socket=socket(AF_INET,SOCK_STREAM,IPPROTO_TCP);
//
//	struct hostent *host;
//	host = gethostbyname(x.c_str());
//
//	SOCKADDR_IN SockAddr;
//	SockAddr.sin_port=htons(80);
//	SockAddr.sin_family=AF_INET;
//	SockAddr.sin_addr.s_addr = *((unsigned long*)host->h_addr);
//
//	if(connect(Socket,(SOCKADDR*)(&SockAddr),sizeof(SockAddr)) != 0)
//		return "";
//
//	string sendbuf = "GET "+page+" HTTP/1.1\r\nHost: "+x+"\r\nConnection: close\r\n\r\n";
//
//	send(Socket,sendbuf.c_str(), sendbuf.length(), 0);
//	char buffer[10000];
//    string buff;
//	int nDataLength =1;
//    ofstream fout("da.html");
//	while (nDataLength != 0)
//	{
//	    buffer[0] = 0;
//		nDataLength = recv(Socket,buffer,10000,0);
//		buff.append (buffer, nDataLength);
//
//	}
//    fout<<buff;
//	closesocket(Socket);
//    WSACleanup();
//
//	return buff;
//}
