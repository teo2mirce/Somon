import numpy as np
import time
from sklearn import model_selection
from sklearn.linear_model import LogisticRegression
from sklearn.tree import DecisionTreeClassifier
from sklearn.neighbors import KNeighborsClassifier
from sklearn.discriminant_analysis import LinearDiscriminantAnalysis
from sklearn.naive_bayes import GaussianNB
from sklearn.preprocessing import normalize
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.datasets import make_moons, make_circles, make_classification
from sklearn.neural_network import MLPClassifier
from sklearn.gaussian_process.kernels import RBF
from sklearn.discriminant_analysis import QuadraticDiscriminantAnalysis
from sklearn.ensemble import RandomForestClassifier, AdaBoostClassifier, GradientBoostingClassifier
from sklearn.svm import SVC, LinearSVC, NuSVC
from sklearn.tree import ExtraTreeClassifier
from sklearn.multiclass import OutputCodeClassifier
from sklearn.multiclass import OneVsOneClassifier
from sklearn.multiclass import OneVsRestClassifier
from sklearn.linear_model.stochastic_gradient import SGDClassifier
from sklearn.linear_model.ridge import RidgeClassifierCV
from sklearn.linear_model.ridge import RidgeClassifier
from sklearn.linear_model.passive_aggressive import PassiveAggressiveClassifier    
from sklearn.gaussian_process.gpc import GaussianProcessClassifier
from sklearn.ensemble.voting_classifier import VotingClassifier
from sklearn.ensemble.bagging import BaggingClassifier
from sklearn.ensemble.forest import ExtraTreesClassifier
from sklearn.naive_bayes import BernoulliNB
from sklearn.calibration import CalibratedClassifierCV
from sklearn.semi_supervised import LabelPropagation
from sklearn.semi_supervised import LabelSpreading
from sklearn.linear_model import LogisticRegressionCV
from sklearn.naive_bayes import MultinomialNB  
from sklearn.neighbors import NearestCentroid
from sklearn.linear_model import Perceptron
from sklearn.mixture import GaussianMixture

import warnings
warnings.filterwarnings("ignore")

import sys

f = open(sys.argv[1])#Train
data = np.loadtxt(f,delimiter=",")
X_Train=data[:, 1:]
Y_Train=data[:,0]

f = open(sys.argv[2])#Test
data = np.loadtxt(f,delimiter=",")
X_Test=data[:, 1:]
Y_Test=data[:,0]

Functions=","+sys.argv[3]+"," 
Norm=sys.argv[4]

models = []
if ",LR," in Functions:
	models.append(('LR', LogisticRegression()))
if ",LDA," in Functions:
	models.append(('LDA', LinearDiscriminantAnalysis()))
if ",KNNC1," in Functions:
	models.append(('KNNC1',     KNeighborsClassifier(1) ))
if ",KNNC9D," in Functions:
	models.append(('KNNC9D',     KNeighborsClassifier(9, weights='distance') ))
if ",DTC," in Functions:
	models.append(('DTC',     DecisionTreeClassifier(max_depth=5) ))
if ",RFC," in Functions:
	models.append(('RFC',     RandomForestClassifier(max_depth=5, n_estimators=10, max_features=1) ))
if ",MLPC," in Functions:
	models.append(('MLPC',     MLPClassifier(alpha=0.1) ))
if ",ABC," in Functions:
	models.append(('ABC',     AdaBoostClassifier() ))
if ",GNB," in Functions:
	models.append(('GNB',     GaussianNB() ))
if ",QDA," in Functions:
	models.append(('QDA',     QuadraticDiscriminantAnalysis()  ))
if ",GBC," in Functions:
	models.append(('GBC',     GradientBoostingClassifier()  ))
if ",ETC," in Functions:
	models.append(('ETC',     ExtraTreeClassifier()  ))
if ",BC," in Functions:
	models.append(('BC',     BaggingClassifier()  ))
if ",SGDC," in Functions:
	models.append(('SGDC',     SGDClassifier()  ))
if ",RC," in Functions:
	models.append(('RC',     RidgeClassifier()  ))
if ",PAC," in Functions:
	models.append(('PAC',     PassiveAggressiveClassifier()  ))
if ",ETSC," in Functions:
	models.append(('ETSC',     ExtraTreesClassifier()  ))
if ",BNB," in Functions:
	models.append(('BNB',     BernoulliNB()  ))
if ",GM," in Functions:
	models.append(('GM',     GaussianMixture()  ))

from sklearn.model_selection import KFold
from collections import Counter

Predictii=[  []  for _ in range(len(Y_Test))]
	
Accs=[]

normlist=[]
if Norm=="N1":
	normlist.append("N1")
if Norm=="None":
	normlist.append("None")
if Norm=="Both":
	normlist.append("N1")
	normlist.append("None")

for normalizare in normlist:

	if(normalizare=="None"):
		X_Test_N=X_Test
		X_Train_N=X_Train
	if(normalizare=="N1"):
		X_Train_N=normalize(X_Train)
		X_Test_N=normalize(X_Test)
	
	for name, model in models:
		start_time = time.time()
		
		kf = KFold(n_splits=10,shuffle=True)
		kf.get_n_splits(X_Train_N)
		miniacc=[]
		for train_index, test_index in kf.split(X_Train_N):
			X_Tr, X_Te= X_Train_N[train_index], X_Train_N[test_index]
			Y_Tr, Y_Te= Y_Train[train_index], Y_Train[test_index]
			model.fit(X_Tr,Y_Tr)
			miniacc.append( (model.predict(X_Te)==Y_Te).mean() )
			
		model.fit(X_Train_N,Y_Train)
		Preds=model.predict(X_Test_N)
		acc=(np.array(miniacc)).mean()
		print(normalizare,' ',name,' ',acc,' time: ',time.time() - start_time)
						
		if len(Accs)<=10 or acc>=np.array(Accs).mean():
			Accs.append(acc)
			for i in range(0,len(Preds)):
				Predictii[i].append(Preds[i])
		
BestIndex=np.array([x for x in Accs]).argsort()[::-1][:10]

for i in range(len(Predictii)):
	Pred=np.array(Predictii[i])[BestIndex]
	print(Counter(Pred).most_common(1)[0][0],' ',100.0*Counter(Pred).most_common(1)[0][1]/(len(Pred)),'%')

