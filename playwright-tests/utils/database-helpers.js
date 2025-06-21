import { exec } from 'child_process';
import { promisify } from 'util';

const execAsync = promisify(exec);

export async function refreshDatabase() {
  try {
    console.log('Refreshing database...');
    const { stdout, stderr } = await execAsync('cd /home/kenneth/dev/gaming-platform && php artisan migrate:fresh --seed --force');
    console.log('Database refresh stdout:', stdout);
    if (stderr) console.log('Database refresh stderr:', stderr);
  } catch (error) {
    console.error('Database refresh failed:', error.message);
    throw error;
  }
}

export async function createUser(userData = {}) {
  const timestamp = Date.now();
  const defaultUser = {
    first_name: 'Test',
    last_name: 'User',
    email: `test-${timestamp}@example.com`,
    password: 'password123',
    role: 'customer',
    ...userData
  };
  
  try {
    const command = `cd /home/kenneth/dev/gaming-platform && php artisan tinker --execute="
      \\$user = \\App\\Models\\User::create([
        'first_name' => '${defaultUser.first_name}',
        'last_name' => '${defaultUser.last_name}',
        'email' => '${defaultUser.email}',
        'password' => \\Illuminate\\Support\\Facades\\Hash::make('${defaultUser.password}'),
        'role' => '${defaultUser.role}',
        'email_verified_at' => ${userData.email_verified_at ? `'${userData.email_verified_at}'` : 'now()'}
      ]);
      
      if ('${defaultUser.role}' === 'customer') {
        \\App\\Models\\customer\\Customer::create(['user_id' => \\$user->id]);
      } elseif ('${defaultUser.role}' === 'creator') {
        \\App\\Models\\creator\\Creator::create([
          'user_id' => \\$user->id,
          'bio' => 'Test creator bio',
          'gaming_pseudo' => 'TestCreator',
          'timezone' => 'America/Toronto',
          'setup_completed' => true
        ]);
      }
      
      echo 'USER_JSON_START' . json_encode(\\$user->toArray()) . 'USER_JSON_END';
    "`;
    
    console.log('Creating user with command:', command);
    console.log('User data:', defaultUser);
    
    const { stdout } = await execAsync(command);
    const output = stdout.trim();
    console.log('Tinker output:', output);
    
    // Extract JSON between markers
    const startMarker = 'USER_JSON_START';
    const endMarker = 'USER_JSON_END';
    const startIndex = output.indexOf(startMarker);
    const endIndex = output.indexOf(endMarker);
    
    if (startIndex !== -1 && endIndex !== -1) {
      const jsonStr = output.substring(startIndex + startMarker.length, endIndex);
      return JSON.parse(jsonStr);
    }
    
    // Fallback to regex
    const jsonMatch = output.match(/\{.*\}$/s);
    if (jsonMatch) {
      return JSON.parse(jsonMatch[0]);
    }
    
    throw new Error('No valid JSON found in tinker output');
  } catch (error) {
    console.error('User creation failed:', error.message);
    console.error('Full error:', error);
    return null;
  }
}

export async function createCreator(userData = {}) {
  const user = await createUser({ role: 'creator', ...userData });
  if (!user) return null;
  
  try {
    const defaultCreator = {
      bio: 'Expert gaming coach',
      gaming_pseudo: 'TestCreator',
      timezone: 'America/Toronto',
      setup_completed: true,
      ...userData
    };
    
    const command = `cd /home/kenneth/dev/gaming-platform && php artisan tinker --execute="
      \\$creator = \\App\\Models\\creator\\Creator::create([
        'user_id' => ${user.id},
        'bio' => '${defaultCreator.bio}',
        'gaming_pseudo' => '${defaultCreator.gaming_pseudo}',
        'timezone' => '${defaultCreator.timezone}',
        'setup_completed' => ${defaultCreator.setup_completed ? 'true' : 'false'}
      ]);
      echo 'CREATOR_JSON_START' . json_encode(\\$creator->toArray()) . 'CREATOR_JSON_END';
    "`;
    
    const { stdout } = await execAsync(command);
    const output = stdout.trim();
    console.log('Creator tinker output:', output);
    
    // Extract JSON between markers
    const startMarker = 'CREATOR_JSON_START';
    const endMarker = 'CREATOR_JSON_END';
    const startIndex = output.indexOf(startMarker);
    const endIndex = output.indexOf(endMarker);
    
    if (startIndex !== -1 && endIndex !== -1) {
      const jsonStr = output.substring(startIndex + startMarker.length, endIndex);
      return { user, creator: JSON.parse(jsonStr) };
    }
    
    // Fallback to regex
    const jsonMatch = output.match(/\{.*\}$/s);
    if (jsonMatch) {
      return { user, creator: JSON.parse(jsonMatch[0]) };
    }
    throw new Error('No valid JSON found in creator creation output');
  } catch (error) {
    console.error('Creator creation failed:', error.message);
    return { user, creator: null };
  }
}
