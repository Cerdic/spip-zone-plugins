<?php
/*************************************************************************************
 * cpp.php
 * -------
 * Author: Iulian M
 * Copyright: (c) 2006 Iulian M
 * Release Version: 1.0.8.9
 * Date Started: 2004/09/27
 *
 * C++ (with QT extensions) language file for GeSHi.
 *
 * CHANGES
 * -------
 * 2009/06/28 (1.0.8.4)
 *   -  Updated list of Keywords from Qt 4.5
 *
 * 2008/05/23 (1.0.7.22)
 *   -  Added description of extra language features (SF#1970248)
 *
 * TODO
 * ----
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/

$language_data = array (
    'LANG_NAME' => 'C++ (QT)',
    'COMMENT_SINGLE' => array(1 => '//', 2 => '#'),
    'COMMENT_MULTI' => array('/*' => '*/'),
    'COMMENT_REGEXP' => array(
        //Multiline-continued single-line comments
        1 => '/\/\/(?:\\\\\\\\|\\\\\\n|.)*$/m',
        //Multiline-continued preprocessor define
        2 => '/#(?:\\\\\\\\|\\\\\\n|.)*$/m'
        ),
    'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
    'QUOTEMARKS' => array("'", '"'),
    'ESCAPE_CHAR' => '',
    'ESCAPE_REGEXP' => array(
        //Simple Single Char Escapes
        1 => "#\\\\[\\\\abfnrtv\'\"?\n]#i",
        //Hexadecimal Char Specs
        2 => "#\\\\x[\da-fA-F]{2}#",
        //Hexadecimal Char Specs
        3 => "#\\\\u[\da-fA-F]{4}#",
        //Hexadecimal Char Specs
        4 => "#\\\\U[\da-fA-F]{8}#",
        //Octal Char Specs
        5 => "#\\\\[0-7]{1,3}#"
        ),
    'NUMBERS' =>
        GESHI_NUMBER_INT_BASIC | GESHI_NUMBER_INT_CSTYLE | GESHI_NUMBER_BIN_PREFIX_0B |
        GESHI_NUMBER_OCT_PREFIX | GESHI_NUMBER_HEX_PREFIX | GESHI_NUMBER_FLT_NONSCI |
        GESHI_NUMBER_FLT_NONSCI_F | GESHI_NUMBER_FLT_SCI_SHORT | GESHI_NUMBER_FLT_SCI_ZERO,
    'KEYWORDS' => array(
        1 => array(
            'case', 'continue', 'default', 'do', 'else', 'for', 'goto', 'if', 'return',
            'switch', 'while', 'delete', 'new', 'this'
            ),
        2 => array(
            'NULL', 'false', 'break', 'true', 'enum', 'errno', 'EDOM',
            'ERANGE', 'FLT_RADIX', 'FLT_ROUNDS', 'FLT_DIG', 'DBL_DIG', 'LDBL_DIG',
            'FLT_EPSILON', 'DBL_EPSILON', 'LDBL_EPSILON', 'FLT_MANT_DIG', 'DBL_MANT_DIG',
            'LDBL_MANT_DIG', 'FLT_MAX', 'DBL_MAX', 'LDBL_MAX', 'FLT_MAX_EXP', 'DBL_MAX_EXP',
            'LDBL_MAX_EXP', 'FLT_MIN', 'DBL_MIN', 'LDBL_MIN', 'FLT_MIN_EXP', 'DBL_MIN_EXP',
            'LDBL_MIN_EXP', 'CHAR_BIT', 'CHAR_MAX', 'CHAR_MIN', 'SCHAR_MAX', 'SCHAR_MIN',
            'UCHAR_MAX', 'SHRT_MAX', 'SHRT_MIN', 'USHRT_MAX', 'INT_MAX', 'INT_MIN',
            'UINT_MAX', 'LONG_MAX', 'LONG_MIN', 'ULONG_MAX', 'HUGE_VAL', 'SIGABRT',
            'SIGFPE', 'SIGILL', 'SIGINT', 'SIGSEGV', 'SIGTERM', 'SIG_DFL', 'SIG_ERR',
            'SIG_IGN', 'BUFSIZ', 'EOF', 'FILENAME_MAX', 'FOPEN_MAX', 'L_tmpnam',
            'SEEK_CUR', 'SEEK_END', 'SEEK_SET', 'TMP_MAX', 'stdin', 'stdout', 'stderr',
            'EXIT_FAILURE', 'EXIT_SUCCESS', 'RAND_MAX', 'CLOCKS_PER_SEC',
            'virtual', 'public', 'private', 'protected', 'template', 'using', 'namespace',
            'try', 'catch', 'inline', 'dynamic_cast', 'const_cast', 'reinterpret_cast',
            'static_cast', 'explicit', 'friend', 'typename', 'typeid', 'class' ,
            'foreach','connect', 'Q_OBJECT' , 'slots' , 'signals', 'Q_SIGNALS', 'Q_SLOTS',
            'Q_FOREACH', 'QCOMPARE', 'QVERIFY', 'qDebug', 'kDebug', 'QBENCHMARK'
            ),
        3 => array(
            'cin', 'cerr', 'clog', 'cout',
            'printf', 'fprintf', 'snprintf', 'sprintf', 'assert',
            'isalnum', 'isalpha', 'isdigit', 'iscntrl', 'isgraph', 'islower', 'isprint',
            'ispunct', 'isspace', 'isupper', 'isxdigit', 'tolower', 'toupper',
            'exp', 'log', 'log10', 'pow', 'sqrt', 'ceil', 'floor', 'fabs', 'ldexp',
            'frexp', 'modf', 'fmod', 'sin', 'cos', 'tan', 'asin', 'acos', 'atan', 'atan2',
            'sinh', 'cosh', 'tanh', 'setjmp', 'longjmp',
            'va_start', 'va_arg', 'va_end', 'offsetof', 'sizeof', 'fopen', 'freopen',
            'fflush', 'fclose', 'remove', 'rename', 'tmpfile', 'tmpname', 'setvbuf',
            'setbuf', 'vfprintf', 'vprintf', 'vsprintf', 'fscanf', 'scanf', 'sscanf',
            'fgetc', 'fgets', 'fputc', 'fputs', 'getc', 'getchar', 'gets', 'putc',
            'putchar', 'puts', 'ungetc', 'fread', 'fwrite', 'fseek', 'ftell', 'rewind',
            'fgetpos', 'fsetpos', 'clearerr', 'feof', 'ferror', 'perror', 'abs', 'labs',
            'div', 'ldiv', 'atof', 'atoi', 'atol', 'strtod', 'strtol', 'strtoul', 'calloc',
            'malloc', 'realloc', 'free', 'abort', 'exit', 'atexit', 'system', 'getenv',
            'bsearch', 'qsort', 'rand', 'srand', 'strcpy', 'strncpy', 'strcat', 'strncat',
            'strcmp', 'strncmp', 'strcoll', 'strchr', 'strrchr', 'strspn', 'strcspn',
            'strpbrk', 'strstr', 'strlen', 'strerror', 'strtok', 'strxfrm', 'memcpy',
            'memmove', 'memcmp', 'memchr', 'memset', 'clock', 'time', 'difftime', 'mktime',
            'asctime', 'ctime', 'gmtime', 'localtime', 'strftime'
            ),
        4 => array(
            'auto', 'bool', 'char', 'const', 'double', 'float', 'int', 'long', 'longint',
            'register', 'short', 'shortint', 'signed', 'static', 'struct',
            'typedef', 'union', 'unsigned', 'void', 'volatile', 'extern', 'jmp_buf',
            'signal', 'raise', 'va_list', 'ptrdiff_t', 'size_t', 'FILE', 'fpos_t',
            'div_t', 'ldiv_t', 'clock_t', 'time_t', 'tm', 'wchar_t',

            'int8', 'int16', 'int32', 'int64',
            'uint8', 'uint16', 'uint32', 'uint64',

            'int_fast8_t', 'int_fast16_t', 'int_fast32_t', 'int_fast64_t',
            'uint_fast8_t', 'uint_fast16_t', 'uint_fast32_t', 'uint_fast64_t',

            'int_least8_t', 'int_least16_t', 'int_least32_t', 'int_least64_t',
            'uint_least8_t', 'uint_least16_t', 'uint_least32_t', 'uint_least64_t',

            'int8_t', 'int16_t', 'int32_t', 'int64_t',
            'uint8_t', 'uint16_t', 'uint32_t', 'uint64_t',

            'intmax_t', 'uintmax_t', 'intptr_t', 'uintptr_t'
            ),
        5 => array(
            "Q_UINT16", "Q_UINT32", "Q_UINT64", "Q_UINT8", "Q_ULLONG",
            "Q_ULONG", "Q3Accel", "Q3Action", "Q3ActionGroup", "Q3AsciiBucket",
            "Q3AsciiCache", "Q3AsciiCacheIterator", "Q3AsciiDict",
            "Q3AsciiDictIterator", "Q3BaseBucket", "Q3BoxLayout", "Q3Button",
            "Q3ButtonGroup", "Q3Cache", "Q3CacheIterator", "Q3Canvas",
            "Q3CanvasEllipse", "Q3CanvasItem", "Q3CanvasItemList",
            "Q3CanvasLine", "Q3CanvasPixmap", "Q3CanvasPixmapArray",
            "Q3CanvasPolygon", "Q3CanvasPolygonalItem", "Q3CanvasRectangle",
            "Q3CanvasSpline", "Q3CanvasSprite", "Q3CanvasText", "Q3CanvasView",
            "Q3CheckListItem", "Q3CheckTableItem", "Q3CleanupHandler",
            "Q3ColorDrag", "Q3ComboBox", "Q3ComboTableItem", "Q3CString",
            "Q3DataBrowser", "Q3DataTable", "Q3DataView", "Q3DateEdit",
            "Q3DateTimeEdit", "Q3DateTimeEditBase", "Q3DeepCopy", "Q3Dict",
            "Q3DictIterator", "Q3Dns", "Q3DnsSocket", "Q3DockArea",
            "Q3DockAreaLayout", "Q3DockWindow", "Q3DragObject", "Q3DropSite",
            "Q3EditorFactory", "Q3FileDialog", "Q3FileIconProvider",
            "Q3FilePreview", "Q3Frame", "Q3Ftp", "Q3GArray", "Q3GCache",
            "Q3GCacheIterator", "Q3GDict", "Q3GDictIterator", "Q3GList",
            "Q3GListIterator", "Q3GListStdIterator", "Q3Grid", "Q3GridLayout",
            "Q3GridView", "Q3GroupBox", "Q3GVector", "Q3HBox", "Q3HBoxLayout",
            "Q3HButtonGroup", "Q3Header", "Q3HGroupBox", "Q3Http",
            "Q3HttpHeader", "Q3HttpRequestHeader", "Q3HttpResponseHeader",
            "Q3IconDrag", "Q3IconDragItem", "Q3IconView", "Q3IconViewItem",
            "Q3ImageDrag", "Q3IntBucket", "Q3IntCache", "Q3IntCacheIterator",
            "Q3IntDict", "Q3IntDictIterator", "Q3ListBox", "Q3ListBoxItem",
            "Q3ListBoxPixmap", "Q3ListBoxText", "Q3ListView", "Q3ListViewItem",
            "Q3ListViewItemIterator", "Q3LNode", "Q3LocalFs", "Q3MainWindow",
            "Q3MemArray", "Q3MimeSourceFactory", "Q3MultiLineEdit",
            "Q3NetworkOperation", "Q3NetworkProtocol", "Q3NetworkProtocolDict",
            "Q3NetworkProtocolFactory", "Q3NetworkProtocolFactoryBase",
            "Q3ObjectDictionary", "Q3PaintDeviceMetrics", "Q3Painter",
            "Q3Picture", "Q3PointArray", "Q3PolygonScanner", "Q3PopupMenu",
            "Q3Process", "Q3ProgressBar", "Q3ProgressDialog", "Q3PtrBucket",
            "Q3PtrCollection", "Q3PtrDict", "Q3PtrDictIterator", "Q3PtrList",
            "Q3PtrListIterator", "Q3PtrListStdIterator", "Q3PtrQueue",
            "Q3PtrStack", "Q3PtrVector", "Q3RangeControl", "Q3ScrollView",
            "Q3Semaphore", "Q3ServerSocket", "Q3Shared", "Q3Signal",
            "Q3SimpleRichText", "Q3SingleCleanupHandler", "Q3Socket",
            "Q3SocketDevice", "Q3SortedList", "Q3SpinWidget", "Q3SqlCursor",
            "Q3SqlEditorFactory", "Q3SqlFieldInfo", "Q3SqlFieldInfoList",
            "Q3SqlForm", "Q3SqlPropertyMap", "Q3SqlRecordInfo",
            "Q3SqlSelectCursor", "Q3StoredDrag", "Q3StrIList", "Q3StringBucket",
            "Q3StrIVec", "Q3StrList", "Q3StrListIterator", "Q3StrVec",
            "Q3StyleSheet", "Q3StyleSheetItem", "Q3SyntaxHighlighter",
            "Q3TabDialog", "Q3Table", "Q3TableItem", "Q3TableSelection",
            "Q3TextBrowser", "Q3TextDrag", "Q3TextEdit",
            "Q3TextEditOptimPrivate", "Q3TextStream", "Q3TextView",
            "Q3TimeEdit", "Q3ToolBar", "Q3TSFUNC", "Q3UriDrag", "Q3Url",
            "Q3UrlOperator", "Q3ValueList", "Q3ValueListConstIterator",
            "Q3ValueListIterator", "Q3ValueStack", "Q3ValueVector", "Q3VBox",
            "Q3VBoxLayout", "Q3VButtonGroup", "Q3VGroupBox", "Q3WhatsThis",
            "Q3WidgetStack", "Q3Wizard", "QAbstractButton",
            "QAbstractEventDispatcher", "QAbstractExtensionFactory",
            "QAbstractExtensionManager", "QAbstractFileEngine",
            "QAbstractFileEngineHandler", "QAbstractFileEngineIterator",
            "QAbstractFormBuilder", "QAbstractGraphicsShapeItem",
            "QAbstractItemDelegate", "QAbstractItemModel", "QAbstractItemView",
            "QAbstractListModel", "QAbstractMessageHandler",
            "QAbstractNetworkCache", "QAbstractPageSetupDialog",
            "QAbstractPrintDialog", "QAbstractProxyModel",
            "QAbstractScrollArea", "QAbstractSlider", "QAbstractSocket",
            "QAbstractSpinBox", "QAbstractTableModel",
            "QAbstractTextDocumentLayout", "QAbstractUndoItem",
            "QAbstractUriResolver", "QAbstractXmlNodeModel",
            "QAbstractXmlReceiver", "QAccessible", "QAccessible2Interface",
            "QAccessibleApplication", "QAccessibleBridge",
            "QAccessibleBridgeFactoryInterface", "QAccessibleBridgePlugin",
            "QAccessibleEditableTextInterface", "QAccessibleEvent",
            "QAccessibleFactoryInterface", "QAccessibleInterface",
            "QAccessibleInterfaceEx", "QAccessibleObject",
            "QAccessibleObjectEx", "QAccessiblePlugin",
            "QAccessibleSimpleEditableTextInterface",
            "QAccessibleTableInterface", "QAccessibleTextInterface",
            "QAccessibleValueInterface", "QAccessibleWidget",
            "QAccessibleWidgetEx", "QAction", "QActionEvent", "QActionGroup",
            "QApplication", "QArgument", "QAssistantClient", "QAtomicInt",
            "QAtomicPointer", "QAuthenticator", "QBasicAtomicInt",
            "QBasicAtomicPointer", "QBasicTimer", "QBitArray", "QBitmap",
            "QBitRef", "QBool", "QBoxLayout", "QBrush", "QBrushData", "QBuffer",
            "QButtonGroup", "QByteArray", "QByteArrayMatcher", "QByteRef",
            "QCache", "QCalendarWidget", "QCDEStyle", "QChar", "QCharRef",
            "QCheckBox", "QChildEvent", "QCleanlooksStyle", "QClipboard",
            "QClipboardEvent", "QCloseEvent", "QColor", "QColorDialog",
            "QColorGroup", "QColormap", "QColumnView", "QComboBox",
            "QCommandLinkButton", "QCommonStyle", "QCompleter",
            "QConicalGradient", "QConstString", "QContextMenuEvent", "QCOORD",
            "QCoreApplication", "QCryptographicHash", "QCursor", "QCursorShape",
            "QCustomEvent", "QDataStream", "QDataWidgetMapper", "QDate",
            "QDateEdit", "QDateTime", "QDateTimeEdit", "QDB2Driver",
            "QDB2Result", "QDBusAbstractAdaptor", "QDBusAbstractInterface",
            "QDBusArgument", "QDBusConnection", "QDBusConnectionInterface",
            "QDBusContext", "QDBusError", "QDBusInterface", "QDBusMessage",
            "QDBusMetaType", "QDBusObjectPath", "QDBusPendingCall",
            "QDBusPendingCallWatcher", "QDBusPendingReply",
            "QDBusPendingReplyData", "QDBusReply", "QDBusServer",
            "QDBusSignature", "QDBusVariant", "QDebug",
            "QDesignerActionEditorInterface", "QDesignerBrushManagerInterface",
            "QDesignerComponents", "QDesignerContainerExtension",
            "QDesignerCustomWidgetCollectionInterface",
            "QDesignerCustomWidgetInterface", "QDesignerDnDItemInterface",
            "QDesignerDynamicPropertySheetExtension", "QDesignerExportWidget",
            "QDesignerExtraInfoExtension", "QDesignerFormEditorInterface",
            "QDesignerFormEditorPluginInterface", "QDesignerFormWindowCursorInterface",
            "QDesignerFormWindowInterface", "QDesignerFormWindowManagerInterface",
            "QDesignerFormWindowToolInterface",
            "QDesignerIconCacheInterface", "QDesignerIntegrationInterface",
            "QDesignerLanguageExtension", "QDesignerLayoutDecorationExtension",
            "QDesignerMemberSheetExtension", "QDesignerMetaDataBaseInterface",
            "QDesignerMetaDataBaseItemInterface",
            "QDesignerObjectInspectorInterface", "QDesignerPromotionInterface",
            "QDesignerPropertyEditorInterface",
            "QDesignerPropertySheetExtension", "QDesignerResourceBrowserInterface",
            "QDesignerTaskMenuExtension", "QDesignerWidgetBoxInterface",
            "QDesignerWidgetDataBaseInterface", "QDesignerWidgetDataBaseItemInterface",
            "QDesignerWidgetFactoryInterface", "QDesktopServices",
            "QDesktopWidget", "QDial", "QDialog", "QDialogButtonBox", "QDir",
            "QDirIterator", "QDirModel", "QDockWidget", "QDomAttr",
            "QDomCDATASection", "QDomCharacterData", "QDomComment",
            "QDomDocument", "QDomDocumentFragment", "QDomDocumentType",
            "QDomElement", "QDomEntity", "QDomEntityReference",
            "QDomImplementation", "QDomNamedNodeMap", "QDomNode",
            "QDomNodeList", "QDomNotation", "QDomProcessingInstruction",
            "QDomText", "QDoubleSpinBox", "QDoubleValidator", "QDrag",
            "QDragEnterEvent", "QDragLeaveEvent", "QDragMoveEvent",
            "QDragResponseEvent", "QDropEvent", "QDynamicPropertyChangeEvent",
            "QErrorMessage", "QEvent", "QEventLoop", "QEventSizeOfChecker",
            "QExplicitlySharedDataPointer", "QExtensionFactory",
            "QExtensionManager", "QFactoryInterface", "QFile", "QFileDialog",
            "QFileIconProvider", "QFileInfo", "QFileInfoList",
            "QFileInfoListIterator", "QFileOpenEvent", "QFileSystemModel",
            "QFileSystemWatcher", "QFlag", "QFlags", "QFocusEvent",
            "QFocusFrame", "QFont", "QFontComboBox", "QFontDatabase",
            "QFontDialog", "QFontInfo", "QFontMetrics", "QFontMetricsF",
            "QForeachContainer", "QForeachContainerBase", "QFormBuilder",
            "QFormLayout", "QFrame", "QFSFileEngine", "QFtp", "QFuture",
            "QFutureInterface", "QFutureInterfaceBase", "QFutureIterator",
            "QFutureSynchronizer", "QFutureWatcher", "QFutureWatcherBase",
            "QGenericArgument", "QGenericReturnArgument", "QGLColormap",
            "QGLContext", "QGLFormat", "QGLFramebufferObject", "QGlobalStatic",
            "QGlobalStaticDeleter", "QGLPixelBuffer", "QGLWidget", "QGradient",
            "QGradientStop", "QGradientStops", "QGraphicsEllipseItem",
            "QGraphicsGridLayout", "QGraphicsItem", "QGraphicsItemAnimation",
            "QGraphicsItemGroup", "QGraphicsLayout", "QGraphicsLayoutItem",
            "QGraphicsLinearLayout", "QGraphicsLineItem", "QGraphicsPathItem",
            "QGraphicsPixmapItem", "QGraphicsPolygonItem",
            "QGraphicsProxyWidget", "QGraphicsRectItem", "QGraphicsScene",
            "QGraphicsSceneContextMenuEvent", "QGraphicsSceneDragDropEvent",
            "QGraphicsSceneEvent", "QGraphicsSceneHelpEvent",
            "QGraphicsSceneHoverEvent", "QGraphicsSceneMouseEvent",
            "QGraphicsSceneMoveEvent", "QGraphicsSceneResizeEvent",
            "QGraphicsSceneWheelEvent", "QGraphicsSimpleTextItem",
            "QGraphicsSvgItem", "QGraphicsTextItem", "QGraphicsView",
            "QGraphicsWidget", "QGridLayout", "QGroupBox", "QGtkStyle", "QHash",
            "QHashData", "QHashDummyNode", "QHashDummyValue", "QHashIterator",
            "QHashNode", "QHBoxLayout", "QHeaderView", "QHelpContentItem",
            "QHelpContentModel", "QHelpContentWidget", "QHelpEngine",
            "QHelpEngineCore", "QHelpEvent", "QHelpGlobal", "QHelpIndexModel",
            "QHelpIndexWidget", "QHelpSearchEngine", "QHelpSearchQuery",
            "QHelpSearchQueryWidget", "QHelpSearchResultWidget", "QHideEvent",
            "QHostAddress", "QHostInfo", "QHoverEvent", "QHttp", "QHttpHeader",
            "QHttpRequestHeader", "QHttpResponseHeader", "QIBaseDriver",
            "QIBaseResult", "QIcon", "QIconDragEvent", "QIconEngine",
            "QIconEngineFactoryInterface", "QIconEngineFactoryInterfaceV2",
            "QIconEnginePlugin", "QIconEnginePluginV2", "QIconEngineV2",
            "QIconSet", "QImage", "QImageIOHandler",
            "QImageIOHandlerFactoryInterface", "QImageIOPlugin", "QImageReader",
            "QImageTextKeyLang", "QImageWriter", "QIncompatibleFlag",
            "QInputContext", "QInputContextFactory",
            "QInputContextFactoryInterface", "QInputContextPlugin",
            "QInputDialog", "QInputEvent", "QInputMethodEvent", "Q_INT16",
            "Q_INT32", "Q_INT64", "Q_INT8", "QInternal", "QIntForSize",
            "QIntForType", "QIntValidator", "QIODevice", "Q_IPV6ADDR",
            "QIPv6Address", "QItemDelegate", "QItemEditorCreator",
            "QItemEditorCreatorBase", "QItemEditorFactory", "QItemSelection",
            "QItemSelectionModel", "QItemSelectionRange", "QKeyEvent",
            "QKeySequence", "QLabel", "QLatin1Char", "QLatin1String", "QLayout",
            "QLayoutItem", "QLayoutIterator", "QLCDNumber", "QLibrary",
            "QLibraryInfo", "QLine", "QLinearGradient", "QLineEdit", "QLineF",
            "QLinkedList", "QLinkedListData", "QLinkedListIterator",
            "QLinkedListNode", "QList", "QListData", "QListIterator",
            "QListView", "QListWidget", "QListWidgetItem", "Q_LLONG", "QLocale",
            "QLocalServer", "QLocalSocket", "Q_LONG", "QMacCompatGLenum",
            "QMacCompatGLint", "QMacCompatGLuint", "QMacGLCompatTypes",
            "QMacMime", "QMacPasteboardMime", "QMainWindow", "QMap", "QMapData",
            "QMapIterator", "QMapNode", "QMapPayloadNode", "QMatrix",
            "QMdiArea", "QMdiSubWindow", "QMenu", "QMenuBar",
            "QMenubarUpdatedEvent", "QMenuItem", "QMessageBox",
            "QMetaClassInfo", "QMetaEnum", "QMetaMethod", "QMetaObject",
            "QMetaObjectExtraData", "QMetaProperty", "QMetaType", "QMetaTypeId",
            "QMetaTypeId2", "QMimeData", "QMimeSource", "QModelIndex",
            "QModelIndexList", "QMotifStyle", "QMouseEvent", "QMoveEvent",
            "QMovie", "QMultiHash", "QMultiMap", "QMutableFutureIterator",
            "QMutableHashIterator", "QMutableLinkedListIterator",
            "QMutableListIterator", "QMutableMapIterator",
            "QMutableSetIterator", "QMutableStringListIterator",
            "QMutableVectorIterator", "QMutex", "QMutexLocker", "QMYSQLDriver",
            "QMYSQLResult", "QNetworkAccessManager", "QNetworkAddressEntry",
            "QNetworkCacheMetaData", "QNetworkCookie", "QNetworkCookieJar",
            "QNetworkDiskCache", "QNetworkInterface", "QNetworkProxy",
            "QNetworkProxyFactory", "QNetworkProxyQuery", "QNetworkReply",
            "QNetworkRequest", "QNoDebug", "QNoImplicitBoolCast", "QObject",
            "QObjectCleanupHandler", "QObjectData", "QObjectList",
            "QObjectUserData", "QOCIDriver", "QOCIResult", "QODBCDriver",
            "QODBCResult", "QPageSetupDialog", "QPaintDevice", "QPaintEngine",
            "QPaintEngineState", "QPainter", "QPainterPath",
            "QPainterPathPrivate", "QPainterPathStroker", "QPaintEvent",
            "QPair", "QPalette", "QPen", "QPersistentModelIndex", "QPicture",
            "QPictureFormatInterface", "QPictureFormatPlugin", "QPictureIO",
            "Q_PID", "QPixmap", "QPixmapCache", "QPlainTextDocumentLayout",
            "QPlainTextEdit", "QPlastiqueStyle", "QPluginLoader", "QPoint",
            "QPointer", "QPointF", "QPolygon", "QPolygonF", "QPrintDialog",
            "QPrintEngine", "QPrinter", "QPrinterInfo", "QPrintPreviewDialog",
            "QPrintPreviewWidget", "QProcess", "QProgressBar",
            "QProgressDialog", "QProxyModel", "QPSQLDriver", "QPSQLResult",
            "QPushButton", "QQueue", "QRadialGradient", "QRadioButton",
            "QReadLocker", "QReadWriteLock", "QRect", "QRectF", "QRegExp",
            "QRegExpValidator", "QRegion", "QResizeEvent", "QResource",
            "QReturnArgument", "QRgb", "QRubberBand", "QRunnable",
            "QScriptable", "QScriptClass", "QScriptClassPropertyIterator",
            "QScriptContext", "QScriptContextInfo", "QScriptContextInfoList",
            "QScriptEngine", "QScriptEngineAgent", "QScriptEngineDebugger",
            "QScriptExtensionInterface", "QScriptExtensionPlugin",
            "QScriptString", "QScriptSyntaxCheckResult", "QScriptValue",
            "QScriptValueIterator", "QScriptValueList", "QScrollArea",
            "QScrollBar", "QSemaphore", "QSessionManager", "QSet",
            "QSetIterator", "QSettings", "QSharedData", "QSharedDataPointer",
            "QSharedMemory", "QSharedPointer", "QShortcut", "QShortcutEvent",
            "QShowEvent", "QSignalMapper", "QSignalSpy", "QSimpleXmlNodeModel",
            "QSize", "QSizeF", "QSizeGrip", "QSizePolicy", "QSlider",
            "QSocketNotifier", "QSortFilterProxyModel", "QSound",
            "QSourceLocation", "QSpacerItem", "QSpinBox", "QSplashScreen",
            "QSplitter", "QSplitterHandle", "QSpontaneKeyEvent", "QSqlDatabase",
            "QSqlDriver", "QSqlDriverCreator", "QSqlDriverCreatorBase",
            "QSqlDriverFactoryInterface", "QSqlDriverPlugin", "QSqlError",
            "QSqlField", "QSqlIndex", "QSQLite2Driver", "QSQLite2Result",
            "QSQLiteDriver", "QSQLiteResult", "QSqlQuery", "QSqlQueryModel",
            "QSqlRecord", "QSqlRelation", "QSqlRelationalDelegate",
            "QSqlRelationalTableModel", "QSqlResult", "QSqlTableModel", "QSsl",
            "QSslCertificate", "QSslCipher", "QSslConfiguration", "QSslError",
            "QSslKey", "QSslSocket", "QStack", "QStackedLayout",
            "QStackedWidget", "QStandardItem", "QStandardItemEditorCreator",
            "QStandardItemModel", "QStatusBar", "QStatusTipEvent",
            "QStdWString", "QString", "QStringList", "QStringListIterator",
            "QStringListModel", "QStringMatcher", "QStringRef", "QStyle",
            "QStyledItemDelegate", "QStyleFactory", "QStyleFactoryInterface",
            "QStyleHintReturn", "QStyleHintReturnMask",
            "QStyleHintReturnVariant", "QStyleOption", "QStyleOptionButton",
            "QStyleOptionComboBox", "QStyleOptionComplex",
            "QStyleOptionDockWidget", "QStyleOptionDockWidgetV2",
            "QStyleOptionFocusRect", "QStyleOptionFrame", "QStyleOptionFrameV2",
            "QStyleOptionFrameV3", "QStyleOptionGraphicsItem",
            "QStyleOptionGroupBox", "QStyleOptionHeader",
            "QStyleOptionMenuItem", "QStyleOptionProgressBar",
            "QStyleOptionProgressBarV2", "QStyleOptionQ3DockWindow",
            "QStyleOptionQ3ListView", "QStyleOptionQ3ListViewItem",
            "QStyleOptionRubberBand", "QStyleOptionSizeGrip",
            "QStyleOptionSlider", "QStyleOptionSpinBox", "QStyleOptionTab",
            "QStyleOptionTabBarBase", "QStyleOptionTabBarBaseV2",
            "QStyleOptionTabV2", "QStyleOptionTabV3",
            "QStyleOptionTabWidgetFrame", "QStyleOptionTitleBar",
            "QStyleOptionToolBar", "QStyleOptionToolBox",
            "QStyleOptionToolBoxV2", "QStyleOptionToolButton",
            "QStyleOptionViewItem", "QStyleOptionViewItemV2",
            "QStyleOptionViewItemV3", "QStyleOptionViewItemV4", "QStylePainter",
            "QStylePlugin", "QSvgGenerator", "QSvgRenderer", "QSvgWidget",
            "QSyntaxHighlighter", "QSysInfo", "QSystemLocale",
            "QSystemSemaphore", "QSystemTrayIcon", "Qt", "Qt3Support",
            "QTabBar", "QTabletEvent", "QTableView", "QTableWidget",
            "QTableWidgetItem", "QTableWidgetSelectionRange", "QTabWidget",
            "QtAlgorithms", "QtAssistant", "QtCleanUpFunction",
            "QtConcurrentFilter", "QtConcurrentMap", "QtConcurrentRun",
            "QtContainerFwd", "QtCore", "QTcpServer", "QTcpSocket", "QtDBus",
            "QtDebug", "QtDesigner", "QTDSDriver", "QTDSResult",
            "QTemporaryFile", "QtEndian", "QTest", "QTestAccessibility",
            "QTestAccessibilityEvent", "QTestData", "QTestDelayEvent",
            "QTestEvent", "QTestEventList", "QTestEventLoop",
            "QTestKeyClicksEvent", "QTestKeyEvent", "QTestMouseEvent",
            "QtEvents", "QTextBlock", "QTextBlockFormat", "QTextBlockGroup",
            "QTextBlockUserData", "QTextBoundaryFinder", "QTextBrowser",
            "QTextCharFormat", "QTextCodec", "QTextCodecFactoryInterface",
            "QTextCodecPlugin", "QTextCursor", "QTextDecoder", "QTextDocument",
            "QTextDocumentFragment", "QTextDocumentWriter", "QTextEdit",
            "QTextEncoder", "QTextFormat", "QTextFragment", "QTextFrame",
            "QTextFrameFormat", "QTextFrameLayoutData", "QTextImageFormat",
            "QTextInlineObject", "QTextIStream", "QTextItem", "QTextLayout",
            "QTextLength", "QTextLine", "QTextList", "QTextListFormat",
            "QTextObject", "QTextObjectInterface", "QTextOption",
            "QTextOStream", "QTextStream", "QTextStreamFunction",
            "QTextStreamManipulator", "QTextTable", "QTextTableCell",
            "QTextTableCellFormat", "QTextTableFormat", "QtGlobal", "QtGui",
            "QtHelp", "QThread", "QThreadPool", "QThreadStorage",
            "QThreadStorageData", "QTime", "QTimeEdit", "QTimeLine", "QTimer",
            "QTimerEvent", "QtMsgHandler", "QtNetwork", "QToolBar",
            "QToolBarChangeEvent", "QToolBox", "QToolButton", "QToolTip",
            "QtOpenGL", "QtPlugin", "QtPluginInstanceFunction", "QTransform",
            "QTranslator", "QTreeView", "QTreeWidget", "QTreeWidgetItem",
            "QTreeWidgetItemIterator", "QTS", "QtScript", "QtScriptTools",
            "QtSql", "QtSvg", "QtTest", "QtUiTools", "QtWebKit", "QtXml",
            "QtXmlPatterns", "QTypeInfo", "QUdpSocket", "QUiLoader",
            "QUintForSize", "QUintForType", "QUndoCommand", "QUndoGroup",
            "QUndoStack", "QUndoView", "QUnixPrintWidget", "QUpdateLaterEvent",
            "QUrl", "QUrlInfo", "QUuid", "QValidator", "QVariant",
            "QVariantComparisonHelper", "QVariantHash", "QVariantList",
            "QVariantMap", "QVarLengthArray", "QVBoxLayout", "QVector",
            "QVectorData", "QVectorIterator", "QVectorTypedData",
            "QWaitCondition", "QWeakPointer", "QWebDatabase", "QWebFrame",
            "QWebHistory", "QWebHistoryInterface", "QWebHistoryItem",
            "QWebHitTestResult", "QWebPage", "QWebPluginFactory",
            "QWebSecurityOrigin", "QWebSettings", "QWebView", "QWhatsThis",
            "QWhatsThisClickedEvent", "QWheelEvent", "QWidget", "QWidgetAction",
            "QWidgetData", "QWidgetItem", "QWidgetItemV2", "QWidgetList",
            "QWidgetMapper", "QWidgetSet", "QWindowsCEStyle", "QWindowsMime",
            "QWindowsMobileStyle", "QWindowsStyle", "QWindowStateChangeEvent",
            "QWindowsVistaStyle", "QWindowsXPStyle", "QWizard", "QWizardPage",
            "QWMatrix", "QWorkspace", "QWriteLocker", "QX11EmbedContainer",
            "QX11EmbedWidget", "QX11Info", "QXmlAttributes",
            "QXmlContentHandler", "QXmlDeclHandler", "QXmlDefaultHandler",
            "QXmlDTDHandler", "QXmlEntityResolver", "QXmlErrorHandler",
            "QXmlFormatter", "QXmlInputSource", "QXmlItem",
            "QXmlLexicalHandler", "QXmlLocator", "QXmlName", "QXmlNamePool",
            "QXmlNamespaceSupport", "QXmlNodeModelIndex", "QXmlParseException",
            "QXmlQuery", "QXmlReader", "QXmlResultItems", "QXmlSerializer",
            "QXmlSimpleReader", "QXmlStreamAttribute", "QXmlStreamAttributes",
            "QXmlStreamEntityDeclaration", "QXmlStreamEntityDeclarations",
            "QXmlStreamEntityResolver", "QXmlStreamNamespaceDeclaration",
            "QXmlStreamNamespaceDeclarations", "QXmlStreamNotationDeclaration",
            "QXmlStreamNotationDeclarations", "QXmlStreamReader",
            "QXmlStreamStringRef", "QXmlStreamWriter"
            )
        ),
    'SYMBOLS' => array(
        '(', ')', '{', '}', '[', ']', '=', '+', '-', '*', '/', '!', '%', '^', '&', ':', ',', ';', '|', '<', '>'
        ),
    'CASE_SENSITIVE' => array(
        GESHI_COMMENTS => false,
        1 => true,
        2 => true,
        3 => true,
        4 => true,
        5 => true,
        ),
    'STYLES' => array(
        'KEYWORDS' => array(
            1 => 'color: #000000; font-weight:bold;',
            2 => 'color: #0057AE;',
            3 => 'color: #2B74C7;',
            4 => 'color: #0057AE;',
            5 => 'color: #22aadd;'
            ),
        'COMMENTS' => array(
            1 => 'color: #888888;',
            2 => 'color: #006E28;',
            'MULTI' => 'color: #888888; font-style: italic;'
            ),
        'ESCAPE_CHAR' => array(
            0 => 'color: #000099; font-weight: bold;',
            1 => 'color: #000099; font-weight: bold;',
            2 => 'color: #660099; font-weight: bold;',
            3 => 'color: #660099; font-weight: bold;',
            4 => 'color: #660099; font-weight: bold;',
            5 => 'color: #006699; font-weight: bold;',
            'HARD' => '',
            ),
        'BRACKETS' => array(
            0 => 'color: #006E28;'
            ),
        'STRINGS' => array(
            0 => 'color: #BF0303;'
            ),
        'NUMBERS' => array(
            0 => 'color: #B08000;',
            GESHI_NUMBER_BIN_PREFIX_0B => 'color: #208080;',
            GESHI_NUMBER_OCT_PREFIX => 'color: #208080;',
            GESHI_NUMBER_HEX_PREFIX => 'color: #208080;',
            GESHI_NUMBER_FLT_SCI_SHORT => 'color:#800080;',
            GESHI_NUMBER_FLT_SCI_ZERO => 'color:#800080;',
            GESHI_NUMBER_FLT_NONSCI_F => 'color:#800080;',
            GESHI_NUMBER_FLT_NONSCI => 'color:#800080;'
            ),
        'METHODS' => array(
            1 => 'color: #2B74C7;',
            2 => 'color: #2B74C7;',
            3 => 'color: #2B74C7;'
            ),
        'SYMBOLS' => array(
            0 => 'color: #006E28;'
            ),
        'REGEXPS' => array(
            ),
        'SCRIPT' => array(
            )
        ),
    'URLS' => array(
        1 => '',
        2 => '',
        3 => '',
        4 => '',
        5 => 'http://doc.trolltech.com/latest/{FNAMEL}.html'
        ),
    'OOLANG' => true,
    'OBJECT_SPLITTERS' => array(
        1 => '.',
        2 => '::',
        3 => '-&gt;',
        ),
    'REGEXPS' => array(
        ),
    'STRICT_MODE_APPLIES' => GESHI_NEVER,
    'SCRIPT_DELIMITERS' => array(
        ),
    'HIGHLIGHT_STRICT_BLOCK' => array(
        ),
    'TAB_WIDTH' => 4,
    'PARSER_CONTROL' => array(
        'KEYWORDS' => array(
            'DISALLOWED_BEFORE' => "(?<![a-zA-Z0-9\$_\|\#>|^])",
            'DISALLOWED_AFTER' => "(?![a-zA-Z0-9_<\|%\\-])"
        ),
        'OOLANG' => array(
            'MATCH_AFTER' => '~?[a-zA-Z][a-zA-Z0-9_]*',
        )
    )
);

?>