import sys
print('\n'.join(sys.path))

import tensorflow as tf
print('tf file:', tf.__file__)

import tensorflow_intel
print('tensorflow_intel version:', tensorflow_intel.__version__)
