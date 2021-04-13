import os
import random
import numpy
from matplotlib import pyplot
import pandas
import sys



df = pandas.read_csv(sys.argv[1])
DATA=df.values

Col=df.columns[0]
LiniiRele=df.loc[df[Col]==0]
LiniiBune=df.loc[df[Col]==1]


OutPath=sys.argv[2]

os.mkdir(OutPath)
for Col in df.columns:
	mini=min( LiniiRele[Col].min(), LiniiBune[Col].min() )
	maxi=max( LiniiRele[Col].max(), LiniiBune[Col].max() )


	bins = numpy.linspace(mini, maxi, 50)

	pyplot.figure()
	pyplot.hist(LiniiRele[Col], bins, alpha=0.5, label='Class 0 Mean: '+str(round(LiniiRele[Col].mean(),2)),color='r')
	pyplot.hist(LiniiBune[Col], bins, alpha=0.5, label='Class 1 Mean: '+str(round(LiniiBune[Col].mean(),2)),color='b')
	pyplot.legend(loc='upper right')
	pyplot.title(Col)
	# pyplot.show(block=False)
	
	pyplot.savefig(OutPath+'/'+Col)
# pyplot.show()