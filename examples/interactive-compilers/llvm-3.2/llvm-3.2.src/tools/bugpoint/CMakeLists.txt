set(LLVM_LINK_COMPONENTS asmparser instrumentation scalaropts ipo
  linker bitreader bitwriter vectorize)

add_llvm_tool(bugpoint
  BugDriver.cpp
  CrashDebugger.cpp
  ExecutionDriver.cpp
  ExtractFunction.cpp
  FindBugs.cpp
  Miscompilation.cpp
  OptimizerDriver.cpp
  ToolRunner.cpp
  bugpoint.cpp
  )

#FGG added support for OpenME universal plugin interface for online analysis and tuning
target_link_libraries( bugpoint ${OPENME_LIB} ${CJSON_LIB})
