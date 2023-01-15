import re

# Open the input and output files
with open('input.html', 'r') as input_file, open('output.html', 'w') as output_file:
    # Read the contents of the input file
    html = input_file.read()
    
    # Use regular expressions to remove the unwanted characters
    cleaned_html = re.sub(r'[\[\]\{\}"]|animeId|animeImg|animeUrl|animeTitle|releasedDate', '', html)

    # Write the cleaned HTML to the output file
    output_file.write(cleaned_html)
