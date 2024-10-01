from pymongo import MongoClient
import re
from email_validator import validate_email, EmailNotValidError

# MongoDB connection
client = MongoClient('mongodb://localhost:27017/')
db = client['contact_list']
contacts_collection = db['contacts']

def normalize_phone(phone):
    digits = re.sub(r'\D', '', phone)
    
    if len(digits) == 10:
        return f"+1-{digits[:3]}-{digits[3:6]}-{digits[6:]}"
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

