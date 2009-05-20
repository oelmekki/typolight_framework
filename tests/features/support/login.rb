def backend_login
  visit "#{APP_URL}/typolight"
  fill_in("Username", :with => "") 
  fill_in("Password", :with => "")
  webrat_session.select( 'French', :from => 'Back end language' )
  click_button("Login")
end
