# Commonly used webrat steps
# http://github.com/brynary/webrat

When /je debug$/ do
  debugger
end

When /je vais sur "(.+)"$/ do |page_name|
  visit page_name
end

When /je vais sur (.+)$/ do |page_name|
  visit path_to( page_name )
end

When /j'appuie sur "(.*)"$/ do |button|
  click_button(button)
  #follow_redirect! if status == 302
end

When /je suis le lien "(.*)"$/ do |link|
  click_link(link)
end

When /je click sur "(.*)"$/ do |link|
  click_link(link)
end

When /je remplis "(.*)" avec "(.*)"$/ do |field, value|
  fill_in(field, :with => value) 
end

When /je selectionne "(.*)" dans "(.*)"$/ do |value, field|
  select(value, {:from => field}) 
end

When /je coche "(.*)"$/ do |field|
  check(field) 
end

When /je décoche "(.*)"$/ do |field|
  uncheck(field) 
end

When /je choisis "(.*)"$/ do |field|
  choose(field)
end

When /j'attache le fichier "(.*)" au format "(.+)" à "(.*)"$/ do |path, format_type, field|
  attach_file(field, file_path_to(path), format_type)
end

Then /^je dois voir "(.*)"$/ do |text|
  response.body.should =~ /#{text}/m
end

Then /^je ne dois pas voir "(.*)"$/ do |text|
  response.body.should_not =~ /#{text}/m
end

Then /^la checkbox "(.*)" doit être cochée$/ do |label|
  field_labeled(label).should be_checked
end
