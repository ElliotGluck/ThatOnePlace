# That one Place API

API is for internal use, so if you want to recreate this go ahead but it probably won't make much sense outside of the content of our specific application

# Requests
### POST to retreive new random place within 1 mile of latlong
     curl -X POST \
      /place \
      -H 'Content-Type: application/json' \
      -d '{
    	"latlong": "123.456, -987.654"
    }'
