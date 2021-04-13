import numpy as np
import random
import time
from random import randint


import sys

f = open(sys.argv[1])
data = np.loadtxt(f,delimiter=",")
X_Train=data[:, 1:]
Y_Train=data[:,0]
f.close()


secun=int(sys.argv[2])
start_time = time.time()


def MyDi(a,b):
	return a/(abs(b)+0.01)

def RandomBool():
	return randint(0,1)==1
class RandomFunction:
	CateMaiSunt=0
	Variabila=True#doar daca CateMaiSunt=0
	VariabilaIndex=0
	Constanta=0
	Operatie='+'
	Left=None
	Right=None
	Feature=-1
	
	def MakeTerminal(self):#Tre apelat daca CateMaiSunt=0
		self.Variabila=RandomBool()
		if self.Variabila:
			self.VariabilaIndex=randint(0,self.Feature-1)
		else:
			self.Constanta=random.choice([-1,0,0.1,0.5,1,2,10])
	def MakeNeterminal(self):
		self.Operatie=random.choice(['+','-','*','/','abs'])
		self.Left=RandomFunction(self.Feature,randint(0,self.CateMaiSunt-1))
		self.Right=RandomFunction(self.Feature,self.CateMaiSunt-1)
		
	def __init__(self,Featur,Cate=-1):
		self.Feature=Featur
		if Cate==-1:
			self.CateMaiSunt=randint(1,self.Feature)
		else:
			self.CateMaiSunt=Cate
		
		if self.CateMaiSunt!=0:
			self.MakeNeterminal()
		else:
			self.MakeTerminal()
	def __str__(self):
		if self.CateMaiSunt==0:
			if self.Variabila:
				return "(x"+str(self.VariabilaIndex)+")"
			else:
				return "("+str(self.Constanta)+")"
		if self.Operatie=='abs':
			return "(abs("+str(self.Left)+'+'+str(self.Right)+"))"
		if self.Operatie=='/':
			return "(MyDi("+str(self.Left)+','+str(self.Right)+"))"
		return "("+str(self.Left)+self.Operatie+str(self.Right)+")"

	

NrFeat=len(X_Train[0])
from sympy import *
for i in range(NrFeat):
	exec('x'+str(i)+'=symbols(\'x'+str(i)+'\')')
def Calc(Feat,S):
	for i in range(len(Feat)):
		S=S.replace( "x"+str(i) , str(Feat[i])  )
	return eval(S)
	

amaifost=set()
bestAcc=0
Total=len(X_Train)
while True:
	
	ElapsedTime = time.time() - start_time
	
	if ElapsedTime>secun or bestAcc>0.999:
		break

	RF=RandomFunction(NrFeat)
	FUNCTIE_GLOBALA=str(simplify(eval(str(RF))))

	if FUNCTIE_GLOBALA in amaifost:
		continue
	amaifost.add(FUNCTIE_GLOBALA)
	bun=0
	for i,line in enumerate(X_Train):
		R=Calc(line,FUNCTIE_GLOBALA)
		if ( R>=0 and Y_Train[i]==1) or (R<0 and Y_Train[i]==0):
			bun+=1
	NewAcc=bun/Total
	if NewAcc>bestAcc:
		print(FUNCTIE_GLOBALA,' ->',NewAcc)
		bestAcc=NewAcc
	
	
	
	
	