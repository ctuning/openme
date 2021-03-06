/* main.c: defines main() for cc1, cc1plus, etc.
   Copyright (C) 2007, 2010  Free Software Foundation, Inc.

This file is part of GCC.

GCC is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free
Software Foundation; either version 3, or (at your option) any later
version.

GCC is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
for more details.

You should have received a copy of the GNU General Public License
along with GCC; see the file COPYING3.  If not see
<http://www.gnu.org/licenses/>.  */

#include "config.h"
#include "system.h"
#include "coretypes.h"
#include "tm.h"
#include "diagnostic-core.h"
#include "toplev.h"

//FGG added support for OpenME universal plugin interface for online analysis and tuning
#include "openme.h"

int main (int argc, char **argv);

/* We define main() to call toplev_main(), which is defined in toplev.c.
   We do this in a separate file in order to allow the language front-end
   to define a different main(), if it so desires.  */

int
main (int argc, char **argv)
{
  //FGG added OpenME
  int r=0;

  openme_init("UNI_ALCHEMIST_USE", "UNI_ALCHEMIST_PLUGINS", NULL, 0);

  r=toplev_main (argc, argv);

  //FGG
  openme_callback("ALC_FINISH", NULL);

  return r;
}
