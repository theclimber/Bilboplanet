#!/usr/bin/python
# -*- coding: UTF-8 -*-
#******* BEGIN LICENSE BLOCK *****
# Copyright (C) 2010 By Gregoire de Hemptinne
# Contact : greg@theclimber.be
# Website : www.theclimber.be
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#**** END LICENSE BLOCK *****
# To make this script work, we need two packages in ubuntu :
# * translate-toolkit
# * python-django

import sys, os
import re
from translate.storage import po
from django.utils import simplejson
import sys, os, re, urllib
from htmlentitydefs import name2codepoint

# This class is just a
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
            if theme != "index.php" and theme != "planetlibre":
                relpath = 'themes/%s' % theme
                self.extract_from_tpl(relpath, 'index.tpl')
                for file in ['dashboard','menu','profile','social','tribes','write']:
                    relpath = 'themes/%s/user' % theme
                    self.extract_from_tpl(relpath,file+'.tpl')

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
    def __init__(self, type, basedir, relative = True):
        self.type = type
        self.basedir = basedir
        self.files = []
        self.relative = True

    def list_files_recursive(self,relpath=''):
        fullpath = os.path.join(self.basedir,relpath)
        subdirlist = []
        for item in os.listdir(fullpath):
            path = os.path.join(fullpath,item)
            if os.path.isfile(path):
                if re.search('%s$' % self.type, item):
                    if not self.relative:
                        self.files.append(path)
                    elif not relpath:
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

helpmsg = """\n
Welcome on the small i18n application of bilboplanet
====================================================

To run this application you need to use the folowing syntax :
$ python i18n/managei18n.py [ACTION] [BILBOPLANET DIRECTORY]

The following actions are possition :
* extract-all : this is the most used action. This action serve to extract\n
\t\tall the strings of the application and to add them in the existing\n
\t\tpo files to let the user work on the translations
* extract-tpl : this extract all the strings from the themes that are\n
\t\tin the "themes" directory putting them into the i18n/themes.pot file
* extract-php : this extract all the strings from the php files of the application
* update-files : this updated the file i18n/files.txt which contains all the\n
\t\tphp files that need to be readed for the extraction of strings
* merge : this merges the strings from the two .pot files (themes, bilbo) and put\n
\t\tthose strings in the .po files so you can begin translate the strings
* compile : this generates compiled translations that can be used for the\n
\t\tapplication. You must run this script before you can see the changes
* autotranslate : this uses Google-Translate for automatically translate the strings
\t\tof the selected .po file. NOTE: for this action the syntax is a bit\n
\t\tdifferent. Here is an usage example :
    \t\t$ python i18n/managei18n.py autotranslate [PATH_TO_PO_FILE]
* help : show this message
\n\n
"""

def extract_tpl(bilbodir):
    translate = Translation(bilbodir)
    translate.extract_tpl()
    potcontent = translate.gettext_code()

    potfile = open(os.path.join(bilbodir,'i18n/themes.pot'),'w')
    potfile.write(potcontent)
    potfile.close()

    print "\nFile i18n/themes.pot successfully created"

def extract_php(bilbodir):
    potfile = os.path.join(bilbodir,'i18n/bilbo.pot')
    fileList = os.path.join(bilbodir,'i18n/files.txt')
    cmd = "xgettext -kT_gettext -kT_ --from-code utf-8 -d bilbo -o %s -L PHP --no-wrap -f %s" % (potfile, fileList)
    print cmd
    os.system('cd %s' % bilbodir)
    os.system(cmd)

    print "\nFile %s successfully created" % potfile

def update_files(bilbodir):
    dic = Directory('php',bilbodir,True)
    dic.list_files_recursive()
    lists = dic.get_filtered_files(['clearbricks','lib','data','.git'])

    dic = Directory('tpl',bilbodir,True)
    dic.list_files_recursive()
    lists += dic.get_filtered_files(['clearbricks','lib','data','.git'])

    fileList = open(os.path.join(bilbodir,'i18n/files.txt'), 'w')
    fileList.write(lists)
    fileList.close()

def merge(bilbodir):
    i18ndir = os.path.join(bilbodir,'i18n')
    potfile = os.path.join(i18ndir,'all.pot')

    themes = os.path.join(i18ndir,'themes.pot')
    php = os.path.join(i18ndir,'bilbo.pot')
    if os.path.isfile(themes) and os.path.isfile(php):
        os.system('msgcat %s %s -o %s' % (themes,php,potfile))
        print "File %s successfully created based on themes.pot and bilbo.pot\n" % (potfile)
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
    print "\nPo files updates, you can now work on translations"
    print "Or you can also run the autotranslate script"
    print "\nOnce you are finished with translations, \ndon't forget to compile the files\n"

def compile(bilbodir):
    i18ndir = os.path.join(bilbodir,'i18n')
    for item in os.listdir(i18ndir):
        if len(item) == 2 and os.path.isdir(os.path.join(i18ndir,item)):
            pofile = os.path.join(i18ndir,'bilbo_%s.po' % item)
            mofile = os.path.join(i18ndir,"%s/LC_MESSAGES/bilbo.mo" % item)
            if os.path.isfile(pofile) and os.path.isfile(mofile):
                cmd = "msgfmt -c -v -o %s %s" % (mofile,pofile)
                print cmd
                os.system(cmd)
    print "\nThe translations are now compiled and you should see them in the interface\n"

def htmldecode(text):
    """Decode HTML entities in the given text."""
    if type(text) is unicode:
        uchr = unichr
    else:
        uchr = lambda value: value > 255 and unichr(value) or chr(value)
    def entitydecode(match, uchr=uchr):
        entity = match.group(1)
        if entity.startswith('#x'):
            return uchr(int(entity[2:], 16))
        elif entity.startswith('#'):
            return uchr(int(entity[1:]))
        elif entity in name2codepoint:
            return uchr(name2codepoint[entity])
        else:
            return match.group(0)
    charrefpat = re.compile(r'&(#(\d+|x[\da-fA-F]+)|[\w.:-]+);?')
    return charrefpat.sub(entitydecode, text)

def get_translation(sl, tl, text):
    """
    Response is in the format
   '{"responseData": {"translatedText":"Ciao mondo"}, "responseDetails": null, "responseStatus": 200}'''
    """
    if text.startswith('"'): text = text[1:-1]
    params = {'v':'1.0', 'q': text.encode('utf-8')}
    try:
        result = simplejson.load(urllib.urlopen('http://ajax.googleapis.com/ajax/services/language/translate?%s&langpair=%s%%7C%s' % (urllib.urlencode(params), sl, tl)))
    except IOError, e:
        print e
        return ""
    else:
        try:
            status = result['responseStatus']
        except KeyError:
            status = -1
        if status == 200:
            return result['responseData']['translatedText']
        else:
            print "Error %s: Translating string %s" % (status, text)
            return ""

def translate_po(file, sl, tl):
    openfile = po.pofile(open(file))
    nb_elem = len(openfile.units)
    moves = 1
    cur_elem = 0
    for unit in  openfile.units:
        # report progress
        cur_elem += 1
        s = "\r%f %% - (%d msg processed out of %d) " \
                % (100 * float(cur_elem) / float(nb_elem), cur_elem, nb_elem)
        sys.stderr.write(s)
        if not unit.isheader():
            if len(unit.msgid):
                if unit.msgstr==[u'""']:
                    moves += 1
                    unit.msgstr = ['"%s"' % htmldecode(get_translation(sl, tl, x)) for x in unit.msgid ]
        if not bool(moves % 50):
            print "Saving file..."
            openfile.save()
    openfile.save()

def autotranslate(in_pofile):
    to_lang = re.findall(".*bilbo_(.*)\\.po$",in_pofile)[0]
    from_lang = "en"
    print('Translating english to %s' %(to_lang))
    translate_po(in_pofile, "en", to_lang)
    print('Translation done')


if __name__ == "__main__":

    if len(sys.argv) == 3:
        action = sys.argv[1]
        bilbodir = os.path.abspath(sys.argv[2])

        if not action == "autotranslate" and not os.path.isdir(bilbodir):
            print "%s is not a directory" % bilbodir
            exit()

        if action == "extract-tpl":
            extract_tpl(bilbodir)

        elif action == "extract-php":
            extract_php(bilbodir)

        elif action == "update-files":
            update_files(bilbodir)

        elif action == "merge":
            merge(bilbodir)

        elif action == "extract-all":
            update_files(bilbodir)
            extract_tpl(bilbodir)
            extract_php(bilbodir)
            merge(bilbodir)

        elif action == "compile":
            compile(bilbodir)

        elif action == "autotranslate":
            autotranslate(bilbodir)

        elif action == "help":
            print helpmsg

        else:
            print "unknown action\n"
            print helpmsg
            exit()

    else:
        print "unknown action\n"
        print helpmsg
        exit()



