#!/usr/bin/python
import sys, os
import re

# actions : extract, compile, autotranslate
# extract on php files
# extract on tpl files
# Script avec 5 actions possibles :
# 1) extract_themes => cree un fichier themes.pot
# 2) extract_php => cree un fichier bilbo.pot
# 3) merge => merge les messages dans les fichiers .po
# 4) autotranslate lang => utilise google-translate pour creer une traduction
# 5) compile

class Position:
	def __init__(self, filename, line):
		self.filename = filename
		self.line = line

class Translation:
	def __init__(self, basedir):
		self.basedir = basedir
		self.dictionary = {}

	def extract_from_tpl(self,relpath,filename):
		filepath = os.path.join(self.basedir, relpath)
		included_files = []
		file = open(os.path.join(filepath,filename))
		for n,line in enumerate(file.readlines()):
			text = re.findall(".*{_(.*?)}.*",line)
			if text:
				for el in text:
					posn = Position("%s/%s" % (relpath,filename), n+1)
					if self.dictionary.has_key(el):
						self.dictionary[el].append(posn)
					else:
						self.dictionary[el] = [posn]

			included = re.findall(".*{!include:'(.*\.tpl)'}.*",line)
			if included:
				included_files.extend(included)
		
		for filen in included_files:
			self.extract_from_tpl(relpath,filen)

	def extract_tpl(self):
		themes_path = os.path.join(self.basedir, "themes")
		for theme in os.listdir(themes_path):
			# We are in the right directory
			relpath = 'themes/%s' % theme
			self.extract_from_tpl(relpath, 'index.tpl')
	
	def gettext_header(self):
		return """# SOME DESCRIPTIVE TITLE.
# Copyright (C) YEAR THE PACKAGE'S COPYRIGHT HOLDER
# This file is distributed under the same license as the PACKAGE package.
# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
#
#, fuzzy
msgid ""
msgstr ""
"Project-Id-Version: PACKAGE VERSION\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: 2010-10-29 10:55+0200\\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"
"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n"
"Language-Team: LANGUAGE <LL@li.org>\\n"
"Language: \\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=CHARSET\\n"
"Content-Transfer-Encoding: 8bit\\n"\n\n\n"""

	def gettext_code(self):
		result = self.gettext_header()
		for key in self.dictionary:
			out = ""
			for pos in self.dictionary[key]:
				out += "#: %s:%s\n" % (pos.filename, pos.line)
			out += 'msgid "%s"\n' % key
			out += 'msgstr ""\n\n'
			result += out
		return result

class Directory:
	def __init__(self, type, basedir):
		self.type = type
		self.basedir = basedir
		self.files = []

	def list_files_recursive(self,relpath=''):
		fullpath = os.path.join(self.basedir,relpath)
		subdirlist = []
		for item in os.listdir(fullpath):
			path = os.path.join(fullpath,item)
			if os.path.isfile(path):
				if re.search('%s$' % self.type, item):
					if not relpath:
						self.files.append(item)
					else:
						self.files.append("%s/%s" % (relpath,item))
			elif os.path.isdir(path):
				if not relpath:
					subdirlist.append(item)
				else:
					subdirlist.append("%s/%s" % (relpath,item))
		for subdir in subdirlist:
			self.list_files_recursive(subdir)

	def get_filtered_files(self, list):
		result = ""
		if not list:
			return self.files
		filtered_files = []
		for file in self.files:
			add = True
			for el in list:
				if el in file:
					add = False
			if add:
				filtered_files.append(file)
		for f in filtered_files:
			result += "%s\n" % f
		return result



def extract_from_php(path,filename):
	cmd = "xgettext -kT_gettext -kT_ --from-code utf-8 -d my_project -o i18n/my_project.pot -L PHP --no-wrap -f files.txt"


if __name__ == "__main__":

	if len(sys.argv) == 3:
		action = sys.argv[1]
		bilbodir = sys.argv[2]


		if action == "extract-tpl":
			if not os.path.isdir(bilbodir):
				print "%s is not a directory" % bilbodir
				exit()
			translate = Translation(bilbodir)
			translate.extract_tpl()
			potcontent = translate.gettext_code()

			potfile = open(os.path.join(bilbodir,'i18n/themes.pot'),'w')
			potfile.write(potcontent)
			potfile.close()

		elif action == "extract-php":
			if not os.path.isdir(bilbodir):
				print "%s is not a directory" % bilbodir
				exit()
			
			potfile = os.path.join(bilbodir,'i18n/bilbo.pot')
			fileList = os.path.join(bilbodir,'i18n/files.txt')
			cmd = "xgettext -kT_gettext -kT_ --from-code utf-8 -d bilbo -o %s -L PHP --no-wrap -f %s" % (potfile, fileList)
			print cmd
			os.system('cd %s' % bilbodir)
			os.system(cmd)

		elif action == "update-files":
			if not os.path.isdir(bilbodir):
				print "%s is not a directory" % bilbodir
				exit()
			dic = Directory('php',bilbodir)
			dic.list_files_recursive()
			lists = dic.get_filtered_files(['clearbricks','lib'])

			fileList = open(os.path.join(bilbodir,'i18n/files.txt'), 'w')
			fileList.write(lists)
			fileList.close()
		
		elif action == "merge":
			if not os.path.isdir(bilbodir):
				print "%s is not a directory" % bilbodir
				exit()
			# merge
			i18ndir = os.path.join(bilbodir,'i18n')
			potfile = os.path.join(i18ndir,'all.pot')
			
			themes = os.path.join(i18ndir,'themes.pot')
			php = os.path.join(i18ndir,'bilbo.pot')
			if os.path.isfile(themes) and os.path.isfile(php):
				os.system('msgcat %s %s -o %s' % (themes,php,potfile))
			else:
				print "Themes or PHP has not been extracted"
				exit()
			if not os.path.isfile(potfile):
				print "There is no %s file in directory" % potfile
				exit()
			for item in os.listdir(i18ndir):
				if len(item) == 2 and os.path.isdir(os.path.join(i18ndir,item)):
					pofile = os.path.join(i18ndir,'bilbo_%s.po' % item)
					if os.path.isfile(pofile):
						cmd = "msgmerge -U %s %s" % (pofile,potfile)
						print cmd
						os.system(cmd)
		
		elif action == "autotranslate":
			# autotranslate
			print "autotranslate"

		elif action == "help":
			print 'help'
		
		else:
			print "unknown action"
			exit()
	else:
		print "error"
		exit()



