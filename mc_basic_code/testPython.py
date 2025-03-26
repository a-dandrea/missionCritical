import os
os.environ["MKL_DISABLE_FAST_MM"] = "1"  # Disable MKL optimizations
os.environ["OMP_NUM_THREADS"] = "1"  # Limit OpenMP threads to 1
os.environ["NUMEXPR_MAX_THREADS"] = "1"  # Limit NUMEXPR threads

import matplotlib
matplotlib.use('Agg')  # Non-GUI backend
import matplotlib.pyplot as plt

plt.plot([1, 2, 3], [1, 2, 3])  # Very simple plot
plt.savefig('/tmp/test_plot.png')  # Save the plot as a file