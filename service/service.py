import os
import json
import time
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
from pymongo import MongoClient
import re
from email_validator import validate_email, EmailNotValidError

# MongoDB connection
client = MongoClient('mongodb://localhost:27017/')
db = client['contact_list']
contacts_collection = db['contacts']

# Directory to watch
WATCH_DIRECTORY = './api/storage/app/private/contacts'


class ContactHandler(FileSystemEventHandler):
    def on_created():
        pass

def normalize_phone(phone):
    digits = re.sub(r'\D', '', phone)
    
    if len(digits) == 10:
        return f"+1-{digits[:3]}-{digits[3:6]}-{digits[6:]}"
    elif len(digits) == 11 and digits[0] == '0':
        return f'+63-{digits[1:4]}-{digits[4:7]}-{digits[7:]}'
    else:
        raise ValueError(f"Invalid phone number: {phone}")

def validate_contact(contact):
    if 'name' not in contact or not contact['name']:
        raise ValueError("Name is required")
    
    if 'email' not in contact or not contact['email']:
        raise ValueError("Email is required")
    
    try:
        validate_email(contact['email'])
    except EmailNotValidError as e:
        raise ValueError(f"Invalid email: {str(e)}")
    
    if contacts_collection.find_one({"email": contact['email']}):
        raise ValueError(f"Email {contact['email']} already exists in the database")
    
    if 'phone' in contact and contact['phone']:
        contact['phone'] = normalize_phone(contact['phone'])
    
    return contact

def process_file(file_path):
    try:
        with open(file_path, 'r') as file:
            contacts = json.load(file)
            # print(contacts[0])

        if type(contacts) != list:
            raise ValueError("JSON file should contain a list of contacts")
        
        valid_contacts = []
        for contact in contacts:
            try:
                valid_contact = validate_contact(contact)
                valid_contacts.append(valid_contact)
            except ValueError as e:
                print(f'This is an invalid contact: {e}, skipping...')

        if valid_contacts:
            # inserting to database 
            contacts_collection.insert_many(documents=valid_contacts)
            print(f'Inserted {len(valid_contacts)} contact into the database')
    except json.JSONDecodeError:
        print(f"Error: {file_path} is not a valid JSON file")
    except Exception as e:
        print(f"Error processing {file_path}: {str(e)}")


def process_file_indir():
    files = sorted(
        [f for f in os.listdir(WATCH_DIRECTORY) if f.endswith('.json')],
        key=lambda x: os.path.getmtime(os.path.join(WATCH_DIRECTORY, x))
    )
    
    if files:
        latest_file = os.path.join(WATCH_DIRECTORY, files[-1])
        print(f"Processing existing file: {latest_file}")
        process_file(latest_file)

if __name__ == "__main__":
    process_file_indir()
    