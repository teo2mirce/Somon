import numpy as np
from PIL import Image
V=np.zeros(2*2*2)
import sys
img =Image.open( sys.argv[1] )
pixels = img.load() # create the pixel map
for i in range(img.size[0]):    # for every col:
	for j in range(img.size[1]):    # For every row
		r,g,b=img.getpixel((i,j))
		r=int(r/128)
		g=int(g/128)
		b=int(b/128)
		V[r*2*2+g*2+b]+=1
V/=sum(V)
for idx,v in enumerate(V):
	if(idx==0):
		print("%3.3f" % v,end="")
	else:
		print(", %3.3f" % v,end="")
		