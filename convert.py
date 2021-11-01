import json
import mysql.connector
# load data
data = json.load(open("/home/cs143/data/nobel-laureates.json", "r"))


awardee = {}
people = {}
orgranization = {}
institution = {}
prize = {}
# get the id, givenName, and familyName of the first laureate
for laureate in data["laureates"]:
    id = laureate["id"]
    givenName = familyName = gender = birth = date = place = city = country = ""
    orgName = awardYear = category = sortOrder = insName = insCity = insCountry = ""
    if "givenName" in laureate.keys():
        givenName = laureate.get("givenName")["en"]
        familyName = laureate.get("familyName")["en"] if "familyName" in laureate.keys() else ""
        gender = laureate.get("gender", "")
        if "birth" in laureate.keys():
            birth = laureate.get("birth")
            date = birth.get("date", "")
            if "place" in birth.keys():
                place = birth.get("place")
                city = place.get("city")["en"] if "city" in place.keys() else ""
                country = place.get("country")["en"] if "country" in place.keys() else ""
        people[id] = (id, givenName, familyName, gender)
        awardee[id] = (id, date, city, country)
    else:
        orgName = laureate.get("orgName")["en"]
        if "founded" in laureate.keys():
            founded = laureate.get("founded", "")
            date = founded.get("date", "")
            if "place" in founded.keys():
                place = founded.get("place", "")
                city = place.get("city")["en"] if "city" in place.keys() else ""
                country = place.get("country")["en"] if "country" in place.keys() else ""
        orgranization[id] = (id, orgName)
        awardee[id] = (id, date, city, country)
    
    for nobelPrize in laureate["nobelPrizes"]:
        awardYear = nobelPrize.get("awardYear", "")
        category = nobelPrize.get("category")["en"]
        sortOrder = nobelPrize.get("sortOrder", "")
        prize[(id, awardYear)] = (id, awardYear, category, sortOrder)
        if "affiliations" in nobelPrize.keys():
            for affiliation in nobelPrize.get("affiliations"):
                insName = affiliation.get("name")["en"] if "name" in affiliation.keys() else ""
                insCity = affiliation.get("city")["en"] if "city" in affiliation.keys() else ""
                insCountry = affiliation.get("country")["en"] if "country" in affiliation.keys() else ""
                if (id, awardYear, insName) not in institution.keys():
                    institution[(id, awardYear, insName)] = (id, awardYear, insName, insCity, insCountry)

with open('awardee.del', 'w') as Awardee:
    for a in awardee.values():
        Awardee.write('|'.join(a) + '\n')

with open('people.del', 'w') as People:
    for p in people.values():
        People.write('|'.join(p) + '\n')

with open('org.del', 'w') as Org:
    for o in orgranization.values():
        Org.write('|'.join(o) + '\n')

with open('ins.del', 'w') as Ins:
    for i in institution.values():
        Ins.write('|'.join(i) + '\n')

with open('prize.del', 'w') as Prize:
    for p in prize.values():
        Prize.write('|'.join(p) + '\n')

# conn = mysql.connector.connect(user='cs143', password='', host='localhost', database='class_db')
# cursor = conn.cursor()
# cursor.execute('create table Awardee (id int primary key, date date, city varchar(100), country varchar(100));')
# cursor.execute('create table People (id int primary key, givenName varchar(100), familyName varchar(100), gender varchar(10));')
# cursor.execute('create table Organization (id int primary key, orgName varchar(500));')
# cursor.execute('create table Institution (id int, name varchar(500), city varchar(100), country varchar(100), primary key(id, name))')
# cursor.execute('create table Prize (id int, awardYear year, category varchar(100), sortOrder varchar(10), primary key(id, awardYear));')

# insert_Awardee = ("insert into Awardee values (%s, %s, %s, %s)")
# insert_People = ("insert into People values (%s, %s, %s, %s)")
# insert_Org = ("insert into Organization values (%s, %s)")
# insert_Ins = ("insert into Institution values (%s, %s, %s, %s)")
# insert_Prize = ("insert into Prize values (%s, %s, %s, %s)")

# for a in awardee.values():
#     cursor.execute(insert_Awardee, a)
# for p in people.values():
#     cursor.execute(insert_People, p)
# for o in orgranization.values():
#     cursor.execute(insert_Org, o)
# for i in institution.values():
#     cursor.execute(insert_Ins, i)
# for p in prize.values():
#     cursor.execute(insert_Prize, p)

# conn.commit()
# cursor.close()
# conn.close()
# print(id + "\t" + givenName + "\t" + familyName)
