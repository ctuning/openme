set(LLVM_LINK_COMPONENTS ${LLVM_TARGETS_TO_BUILD} bitreader asmparser bitwriter instrumentation scalaropts ipo vectorize)

add_llvm_tool(opt
  AnalysisWrappers.cpp
  GraphPrinters.cpp
  PrintSCC.cpp
  opt.cpp
  )

#FGG added support for OpenME universal plugin interface for online analysis and tuning
target_link_libraries( opt ${OPENME_LIB} ${CJSON_LIB})
