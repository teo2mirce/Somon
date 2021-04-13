#pyinstaller -F --hidden-import sklearn.neighbors.typedefs KC.py
#http://textfiles.com/directory.html


from collections import Counter
import numpy as np


def KernelFrom2ListsK3RN3L(A,B):
	ret=0
	if(len(A)<len(B)):
		for pair in A.keys():
			a=A[pair]
			b=B[pair]
			if(a<b):
				ret+=a/b
			else:
				ret+=b/a
	else:
		for pair in B.keys():
			a=A[pair]
			b=B[pair]
			if(a<b):
				ret+=a/b
			else:
				ret+=b/a
	return ret
	
def KernelFrom2ListsIntersect(A,B):
	ret=0
	if(len(A)<len(B)):
		for pair in A.keys():
			a=A[pair]
			b=B[pair]
			if(a<b):
				ret+=a
			else:
				ret+=b
			# ret+=min(A[pair],B[pair])
	else:
		for pair in B.keys():
			a=A[pair]
			b=B[pair]
			if(a<b):
				ret+=a
			else:
				ret+=b
	return ret
def KernelFrom2ListsSpectrum(A,B):
	ret=0
	if(len(A)<len(B)):
		for pair in A.keys():
			ret+=(1+A[pair])*(1+B[pair])
	else:
		for pair in B.keys():
			ret+=(1+A[pair])*(1+B[pair])
	return ret
def KernelFrom2ListsPresence(A,B):
	ret=0
	if(len(A)<len(B)):
		for pair in A.keys():
			if(B[pair]!=0):
				ret+=1
	else:
		for pair in B.keys():
			if(A[pair]!=0):
				ret+=1
	return ret
def File2Pgrams(file,pgram=2):
	# print(file)
	# oneLongTweet = open(file,encoding='utf8').read()
	oneLongTweet = open(file,encoding='ISO-8859-1').read()
	oneLongTweet = oneLongTweet.lower()
	oneLongTweet=' '.join(oneLongTweet.split())
	return Counter([oneLongTweet[i:i + pgram] for i in range(0, len(oneLongTweet )-pgram +1 )])
	

import os
import sys
Dirs=[entry for entry in os.listdir(sys.argv[1]) if os.path.isdir(sys.argv[1]+'/'+entry)]
assert 'test' in Dirs

X_Train=[]
Y_Train=[]
X_Test=[]

for counter,dir in enumerate(Dirs):
	files=os.listdir(sys.argv[1]+'/'+dir)
	for file in files:
		if dir=='test':
			X_Test.append(sys.argv[1]+'/'+dir+'/'+file)
		else:
			X_Train.append(sys.argv[1]+'/'+dir+'/'+file)
			Y_Train.append(counter)

N_Train=len(X_Train)
N_Test=len(X_Test)
N=N_Train+N_Test



#Make kernel
Kernel=np.empty([N,N], dtype=float)
Cache=np.empty([N, ],dtype=object)


from sklearn.svm import NuSVC
from math import sqrt
from sklearn.model_selection import LeaveOneOut
# from sklearn.cross_validation import LeaveOneOut
Y_Train=np.array(Y_Train)


# import sklearn
# print(sklearn.__version__)

# print("merge",N)
Predictii=[  []  for _ in range(N_Test)]
Accs=[]
ma=0;
#Try all:
# for pgram in range(4,0,-1):#2 
for pgram in range(1,4):#2 
	for normalizare in [False,True]:#1
	
		
		i=0
		for file in X_Train+X_Test:
			Cache[i]=File2Pgrams(file,pgram)
			if normalizare:
				norma=1+sum(Cache[i].values())
				for pair in Cache[i].keys():
					Cache[i][pair]/=norma
				
			i=i+1
	
		for kernelFunc in [KernelFrom2ListsK3RN3L,KernelFrom2ListsIntersect,KernelFrom2ListsSpectrum,KernelFrom2ListsPresence]:#3
		# for kernelFunc in [KernelFrom2ListsK3RN3L,KernelFrom2ListsIntersect]:#3
			for i in range(N):
				for j in range(i,N):
					Kernel[i][j]=kernelFunc(Cache[i],Cache[j])
					Kernel[j][i]=Kernel[i][j]
			#normalizare
			for i in range(N):
				for j in range(N):
					if(i!=j):
						Kernel[i][j]/=sqrt(Kernel[i][i]*Kernel[j][j]+1)

			for i in range(N):
				Kernel[i][i]=1
			for nu in [0.4,0.25,0.1,0.07]:#4
				clf = NuSVC(nu,kernel='precomputed' )#,#verbose =True,      shrinking=False,
				try:#try-ul asta e pt ca pentru diferite nu-uri da exceptie
				#Bun de kfold
					pr=[]
					loo = LeaveOneOut()
					loo.get_n_splits(X_Train)
					for train, test in loo.split(X_Train):
						clf.fit(Kernel[np.ix_(train,train)], Y_Train[train])
						pr.append(clf.predict(Kernel[np.ix_(test,train)])==Y_Train[test])
					print(pgram,normalizare,kernelFunc,nu,np.array(pr).mean())
					
					if np.array(pr).mean()>ma:
						ma=np.array(pr).mean()
						
						m_norm=normalizare
						m_pgram=pgram
						m_func=kernelFunc
						m_nu=nu
						
					if len(Accs)<=10 or np.array(pr).mean()>=np.array(Accs).mean():
						# print(len(Accs))
						Accs.append( np.array(pr).mean() )
						clf.fit(Kernel[0:N_Train,0:N_Train], Y_Train[0:N_Train])
						Preds=clf.predict(Kernel[N_Train:,0:N_Train])
						for i in range(0,len(Preds)):
							Predictii[i].append(Dirs[Preds[i]])
				except:
					pass
	
print("Max acc: ",ma)
print("Norm: ",m_norm)
print("m_pgram: ",m_pgram)
print("m_func: ",m_func)
print("m_nu: ",m_nu)
				

				
BestIndex=np.array([x for x in Accs]).argsort()[::-1][:10]



for i in range(len(Predictii)):
	Pred=np.array(Predictii[i])[BestIndex]
	print(X_Test[i],'->',Counter(Pred).most_common(1)[0][0],' ',100.0*Counter(Pred).most_common(1)[0][1]/(len(Pred)),'%')
			
	
	