import numpy as np
import nltk
from collections import Counter
import string

from nltk.corpus import wordnet as wn
# from nltk.corpus import senseval



from nltk.corpus import stopwords
stop_words = stopwords.words('english')
#https://chrisalbon.com/machine_learning/preprocessing_text/remove_stop_words/

def Text2Pgrams(text,pgram):
	temp=Counter(text[i:i + pgram] for i in range(0, len(text )-pgram +1 ))
	
	norma=1+sum(temp.values())
	for pair in temp.keys():
		temp[pair]/=norma
	return temp
	
def KernelFrom2Lists(A,B):
	ret=0
	if(len(A)<len(B)):
		for pair in A.keys():
			if(B[pair]!=0):
				ret+=B[pair]+A[pair]
	else:
		for pair in B.keys():
			if(A[pair]!=0):
				ret+=B[pair]+A[pair]
	return ret
def Similaritate(A,B,pgram_start,pgram_end):
	ret=0
	for i in range(pgram_start,pgram_end):
		k=KernelFrom2Lists(Text2Pgrams(A,i),Text2Pgrams(B,i))
		ret+=k*i**2
		if(k==0):
			i=9999
	return ret	
def CleanStringFromContext(Words,relatii):
	ret=' '.join(Words)+' '
	for word in Words:
		for syn in wn.synsets(word):
			ret+=' '+CleanStringFromSynset(syn,relatii)
	return ret
def wordnet_pos_code(tag):
    if tag.startswith('NN'):
        return wn.NOUN
    elif tag.startswith('VB'):
        return wn.VERB
    elif tag.startswith('JJ'):
        return wn.ADJ
    elif tag.startswith('RB'):
        return wn.ADV
    else:
        return ''

def CleanString(text):
	text=text.translate(text.maketrans('', '', string.punctuation))
	text=text.split()
	text=[word.lower() for word in text if word not in stop_words]
	return text

#era synseturi linie
def Synseturi(strr):#line
	return [syn for syn in wn.synsets(strr)]
	# POSSinsetLinie=[syn.pos() for syn in SinseturiLinie]

def definition(x):
	return x.definition()
def examples(x):
	return " ".join(x.examples())
def hyponyms(x):
	return " ".join(w.definition() for w in x.hyponyms())
def hypernyms(x):
	return " ".join(w.definition() for w in x.hypernyms())
def member_meronyms(x):
	return " ".join(w.definition() for w in x.member_meronyms())
def part_meronyms(x):
	return " ".join(w.definition() for w in x.part_meronyms())
def substance_meronyms(x):
	return " ".join(w.definition() for w in x.substance_meronyms())
def also_sees(x):
	return " ".join(w.definition() for w in x.also_sees())
def attributes(x):
	return " ".join(w.definition() for w in x.attributes())

relations=[definition,examples,hyponyms,hypernyms,member_meronyms,part_meronyms,substance_meronyms,also_sees,attributes]

import itertools
def findsubsets(S,m):
    return set(itertools.combinations(S, m))

def CleanStringFromSynset(syn,relatii):
	ret = ''
	for rel in relatii:
		ret+=' '+rel(syn)
	return  ' '.join(CleanString(ret))

	
# STR="the nurse gave him a flu shot"
# word="shot"



import sys

# String
# word

TLDR=sys.argv[1] # TLDR/Show
STR=sys.argv[2]
word=sys.argv[3]
# STR="The tank has a top speed of 70 miles an hour, which it can sustain for 3 hours"
# STR="We cannot fill more gasoline in the tank."
# STR="The tank is full of soldiers."
# STR="The tank is full of nitrogen."
# word="tank"

STR=CleanString(STR)
POS=[x[1] for x in nltk.pos_tag(STR) if x[0]==word]
if len(POS) !=1:
	print("Multiple occurrences or not found")
	exit()
POS=wordnet_pos_code(POS[0])

Sinseturi=[syn for syn in Synseturi(word) if syn.pos()==POS]


Fereastra=9#9 este cel mai bun, ca accuratete, la 10 e cam la fel dar +30s, si de la 11 in colo nu mai e ok
pgram_start=4
pgram_end=6


C=Counter()


	


for m in range(1,4):
	for relatii in findsubsets(relations,m):
		# m=2
		# relatii = next(iter(findsubsets(relations,m) ))
		
		relatii=list(relatii)
		CachedString={}
		for syn in Sinseturi:
			CachedString[syn.name()]=''
		for syn in Sinseturi:
			CachedString[syn.name()]+=CleanStringFromSynset(syn,relatii)+' '
		keys=[key for key in CachedString]


		Poz=STR.index(word)
		ContextStanga=[]
		ContextDreapta=[]
		PS=Poz-1
		PD=Poz+1
		while PS>=0 and len(ContextStanga)<Fereastra:
			if len(wn.synsets(STR[PS]))!=0 and STR[PS] not in stop_words:
				ContextStanga.append(STR[PS])
			PS-=1
		while PD<len(STR) and len(ContextDreapta)<Fereastra:
			if len(wn.synsets(STR[PD]))!=0 and STR[PD] not in stop_words:
				ContextDreapta.append(STR[PD])
			PD+=1
		Context=ContextStanga+ContextDreapta
		ContextStr=CleanStringFromContext(Context,relatii)
		Similar=[Similaritate(ContextStr, CachedString[key],pgram_start,pgram_end) for key in CachedString]
		indexSyn=np.argmax(Similar)
		C[indexSyn]+=1

All=sum(C.values())



if TLDR!="TLDR":
	print("Possible synsets")
	for id,syn in enumerate(Sinseturi):
		print(id+1,syn.definition())
	print("Prediction:")
	for (id,nr) in C.most_common():
		print(id+1,'with',100.0*nr/All,'% - ',Sinseturi[id].definition())
else:
	id=C.most_common(1)[0][0]
	print(Sinseturi[id].definition())